import { test, expect } from '@playwright/test';

test.describe('Project Sessions Table', () => {
  test.beforeEach(async ({ page }) => {
    // Login as test user
    await page.goto('/login');
    await page.fill('input[name="email"]', 'user@domain.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    // Wait for successful login - could redirect to dashboard or home
    await page.waitForLoadState('networkidle');

    // Check if we're logged in by looking for a logout link or user menu
    await page.waitForSelector('a[href*="logout"], .user-menu, nav', { timeout: 10000 });
  });

  test('displays sessions table with correct columns', async ({ page }) => {
    // Navigate to a project with sessions
    await page.goto('/project/1'); // Assuming project with ID 1 exists

    // Wait for the page to load
    await page.waitForSelector('h2:has-text("Recent Sessions")');

    // Check if the sessions section is visible
    await expect(page.locator('h2:has-text("Recent Sessions")')).toBeVisible();

    // Check table headers are present
    await expect(page.locator('table thead th:has-text("Date")')).toBeVisible();
    await expect(page.locator('table thead th:has-text("Task")')).toBeVisible();
    await expect(page.locator('table thead th:has-text("Category")')).toBeVisible();
    await expect(page.getByRole('cell', { name: 'Duration' })).toBeVisible();
    await expect(page.locator('table thead th:has-text("Billable")')).toBeVisible();
    await expect(page.locator('table thead th:has-text("Comment")')).toBeVisible();
  });

  test('displays session data in correct format', async ({ page }) => {
    await page.goto('/project/1');
    await page.waitForSelector('h2:has-text("Recent Sessions")');

    // Check if table has data rows
    const tableRows = page.locator('table tbody tr');
    const rowCount = await tableRows.count();

    if (rowCount > 0) {
      const firstRow = tableRows.first();

      // Check date format (should be like "Jan 15, 2024")
      const dateCell = firstRow.locator('td').first();
      const dateText = await dateCell.textContent();
      expect(dateText).toMatch(/[A-Z][a-z]{2} \d{1,2}, \d{4}/);

      // Check duration format (should be like "2h 30m" or "45m")
      const durationCell = firstRow.locator('td').nth(3);
      const durationText = await durationCell.textContent();
      expect(durationText).toMatch(/(\d+h\s)?\d+m/);

      // Check billable status has badge
      const billableCell = firstRow.locator('td').nth(4);
      const badge = billableCell.locator('span');
      await expect(badge).toHaveClass(/bg-(green|gray)-100/);

      const badgeText = (await badge.textContent()).replace(/\s/g, "");
      expect(['Yes', 'No']).toContain(badgeText);
    }
  });

  test('pagination works correctly', async ({ page }) => {
    await page.goto('/project/1');
    await page.waitForSelector('h2:has-text("Recent Sessions")');

    // Check if pagination is present (only if there are more than 15 sessions)
    const paginationLinks = page.locator('.pagination a, .pagination button');
    const paginationCount = await paginationLinks.count();

    if (paginationCount > 0) {
      // Test pagination functionality
      const nextButton = page.locator('a:has-text("Next"), button:has-text("Next")').first();
      const prevButton = page.locator('a:has-text("Previous"), button:has-text("Previous")').first();

      // If next button exists and is enabled, click it
      if (await nextButton.isEnabled()) {
        await nextButton.click();
        await page.waitForLoadState('networkidle');

        // Verify we're on a different page
        expect(page.url()).toContain('page=');

        // Check that sessions are still displayed
        await expect(page.locator('h2:has-text("Recent Sessions")')).toBeVisible();

        // If previous button is now available, test it
        if (await prevButton.isEnabled()) {
          await prevButton.click();
          await page.waitForLoadState('networkidle');
        }
      }
    }
  });

  test('sessions are ordered by date descending (newest first)', async ({ page }) => {
    await page.goto('/project/1');
    await page.waitForSelector('h2:has-text("Recent Sessions")');

    const dateElements = page.locator('table tbody tr td:first-child');
    const dateCount = await dateElements.count();

    if (dateCount > 1) {
      const dates = [];

      // Get all dates from the first page
      for (let i = 0; i < Math.min(dateCount, 5); i++) {
        const dateText = await dateElements.nth(i).textContent();
        dates.push(new Date(dateText));
      }

      // Check that dates are in descending order (newest first)
      for (let i = 1; i < dates.length; i++) {
        expect(dates[i] <= dates[i - 1]).toBeTruthy();
      }
    }
  });

  test('task names are clickable links or properly displayed', async ({ page }) => {
    await page.goto('/project/1');
    await page.waitForSelector('h2:has-text("Recent Sessions")');

    const taskCells = page.locator('table tbody tr td:nth-child(2)');
    const taskCount = await taskCells.count();

    if (taskCount > 0) {
      const firstTaskCell = taskCells.first();
      const taskText = await firstTaskCell.textContent();

      // Task should either show a name or "-" if no task
      expect(taskText.length).toBeGreaterThan(0);
      expect(taskText).not.toBe('');
    }
  });

  test('responsive table scrolls horizontally on small screens', async ({ page }) => {
    // Test mobile responsiveness
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/project/1');
    await page.waitForSelector('h2:has-text("Recent Sessions")');

    const tableContainer = page.locator('.overflow-x-auto');
    await expect(tableContainer).toBeVisible();

    const table = tableContainer.locator('table');
    await expect(table).toBeVisible();

    // Check that table has min-width class for horizontal scrolling
    await expect(table).toHaveClass(/min-w-full/);
  });
});
