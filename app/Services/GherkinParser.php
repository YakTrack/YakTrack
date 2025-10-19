<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\Project;

class GherkinParser
{
    /**
     * Parse a Gherkin file content and extract acceptance criteria
     *
     * @param string $content The Gherkin file content
     * @param Project $project The project to associate with the criteria
     * @return array<int, array{code: string|null, name: string, description: string, feature_id: int|null}>
     */
    public function parse(string $content, Project $project): array
    {
        $lines = explode("\n", $content);
        $criteria = [];
        $currentFeature = null;
        $currentScenario = null;
        $currentDescription = [];
        $scenarioCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines and comments
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Feature line
            if (str_starts_with($line, 'Feature:')) {
                $currentFeature = trim(substr($line, 8));
                continue;
            }

            // Scenario line
            if (str_starts_with($line, 'Scenario:')) {
                $this->saveCurrentScenario($criteria, $currentScenario, $currentDescription, $scenarioCount, $currentFeature, $project);
                
                $currentScenario = trim(substr($line, 9));
                $currentDescription = [];
                $scenarioCount++;
                continue;
            }

            // Scenario Outline line
            if (str_starts_with($line, 'Scenario Outline:')) {
                $this->saveCurrentScenario($criteria, $currentScenario, $currentDescription, $scenarioCount, $currentFeature, $project);
                
                $currentScenario = trim(substr($line, 17));
                $currentDescription = [];
                $scenarioCount++;
                continue;
            }

            // Given, When, Then, And, But steps
            if (preg_match('/^(Given|When|Then|And|But)\s+(.+)$/', $line, $matches)) {
                $currentDescription[] = $line;
                continue;
            }

            // Background line
            if (str_starts_with($line, 'Background:')) {
                continue;
            }

            // Examples line
            if (str_starts_with($line, 'Examples:')) {
                continue;
            }

            // Tags
            if (str_starts_with($line, '@')) {
                continue;
            }

            // Other content (descriptions, etc.)
            if (!empty($line)) {
                $currentDescription[] = $line;
            }
        }

        // Save the last scenario
        $this->saveCurrentScenario($criteria, $currentScenario, $currentDescription, $scenarioCount, $currentFeature, $project);

        return $criteria;
    }

    /**
     * Save the current scenario as acceptance criteria
     */
    private function saveCurrentScenario(
        array &$criteria,
        ?string $scenario,
        array $description,
        int $scenarioCount,
        ?string $feature,
        Project $project
    ): void {
        if (empty($scenario)) {
            return;
        }

        // Generate code based on project and scenario count
        $code = $this->generateCode($project, $scenarioCount);
        
        // Create description from steps
        $descriptionText = implode("\n", $description);

        $criteria[] = [
            'code' => $code,
            'name' => $scenario,
            'description' => $descriptionText,
            'feature_id' => $feature ? Feature::findOrCreateByName($feature, $project->id)->id : null,
        ];
    }

    /**
     * Generate a unique code for the acceptance criteria
     */
    private function generateCode(Project $project, int $scenarioCount): string
    {
        // Get existing codes for this project to avoid duplicates
        $existingCodes = $project->acceptanceCriteria()
            ->whereNotNull('code')
            ->pluck('code')
            ->toArray();

        $baseCode = 'AC-' . str_pad($scenarioCount, 3, '0', STR_PAD_LEFT);
        $code = $baseCode;
        $counter = 1;

        // Ensure uniqueness
        while (in_array($code, $existingCodes)) {
            $code = $baseCode . '-' . $counter;
            $counter++;
        }

        return $code;
    }

    /**
     * Validate Gherkin content
     *
     * @param string $content The Gherkin file content
     * @return array{valid: bool, errors: array<string>}
     */
    public function validate(string $content): array
    {
        $errors = [];
        $lines = explode("\n", $content);
        $hasFeature = false;
        $hasScenario = false;

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            $lineNum = $lineNumber + 1;

            // Skip empty lines and comments
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Check for Feature
            if (str_starts_with($line, 'Feature:')) {
                $hasFeature = true;
                continue;
            }

            // Check for Scenario
            if (str_starts_with($line, 'Scenario:') || str_starts_with($line, 'Scenario Outline:')) {
                $hasScenario = true;
                continue;
            }

            // Validate step format
            if (preg_match('/^(Given|When|Then|And|But)\s+(.+)$/', $line)) {
                continue;
            }

            // Check for invalid keywords
            if (preg_match('/^(Given|When|Then|And|But)\s*$/', $line)) {
                $errors[] = "Line {$lineNum}: Step keyword must be followed by a description";
                continue;
            }

            // Check for unknown keywords
            if (preg_match('/^[A-Z][a-z]+\s*:/', $line)) {
                $keyword = trim(explode(':', $line)[0]);
                if (!in_array($keyword, ['Feature', 'Scenario', 'Scenario Outline', 'Background', 'Examples'])) {
                    $errors[] = "Line {$lineNum}: Unknown keyword '{$keyword}:'";
                }
            }
        }

        if (!$hasFeature) {
            $errors[] = 'File must contain at least one Feature';
        }

        if (!$hasScenario) {
            $errors[] = 'File must contain at least one Scenario or Scenario Outline';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
