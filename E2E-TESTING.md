# End-to-End Testing with Playwright

This document describes how to run and write E2E tests for the YakTrack application using Playwright.

## Setup

### Install Dependencies

```bash
npm install
npx playwright install
```

### Environment Setup

1. Ensure your Laravel application is running with test data
2. Update test credentials in `e2e/helpers/test-data.js` if needed
3. Make sure your database has some test projects, clients, and sessions

## Running Tests

### Run all E2E tests
```bash
npm run test:e2e
```

### Run tests in headed mode (see browser)
```bash
npm run test:e2e:headed
```

### Run tests with UI mode (interactive)
```bash
npm run test:e2e:ui
```

### Run specific test file
```bash
npx playwright test e2e/tests/projects/project-sessions.spec.js
```

### Run tests in specific browser
```bash
npx playwright test --project=chromium
npx playwright test --project=firefox  
npx playwright test --project=webkit
```

## Project Sessions Tests

The `e2e/tests/projects/project-sessions.spec.js` file contains comprehensive tests for the project sessions table functionality:

### Test Coverage

1. **Table Display Tests**
   - Verifies all table columns are present
   - Checks table headers are correctly displayed
   - Ensures table structure is valid

2. **Data Format Tests**
   - Validates date formatting (e.g., "Jan 15, 2024")
   - Checks duration formatting (e.g., "2h 30m" or "45m")
   - Verifies billable status badges are displayed correctly
   - Ensures task names are properly shown

3. **Empty State Tests**
   - Tests the display when no sessions exist
   - Verifies empty state message and icon
   - Checks that the empty state is conditionally rendered

4. **Pagination Tests**
   - Tests pagination controls when applicable
   - Verifies navigation between pages works
   - Ensures session data persists across page changes

5. **Sorting Tests**
   - Confirms sessions are displayed in reverse chronological order
   - Validates newest sessions appear first

6. **Responsive Design Tests**
   - Tests table responsiveness on mobile devices
   - Verifies horizontal scrolling works correctly
   - Ensures table remains usable on small screens

## Test Data Management

The `e2e/helpers/test-data.js` file provides utilities for:
- Creating test clients, projects, tasks, and sessions
- User authentication (admin and regular users)
- Test data cleanup after test runs

### Usage Example

```javascript
const { TestDataHelper } = require('../helpers/test-data');

test('my test', async ({ page }) => {
  const testData = new TestDataHelper(page);
  
  await testData.loginAsAdmin();
  const project = await testData.createProject({ name: 'My Test Project' });
  await testData.createSessions(project.id, 25); // Create 25 test sessions
  
  // Your test code here...
  
  await testData.cleanup();
});
```

## Configuration

The `playwright.config.js` file is configured to:
- Run tests in Chrome, Firefox, and Safari
- Start the Laravel development server automatically
- Generate HTML reports
- Take screenshots on failures
- Record traces for debugging

## Best Practices

1. **Test Independence**: Each test should be able to run independently
2. **Data Cleanup**: Always clean up test data after tests complete
3. **Realistic Data**: Use realistic test data that matches production patterns
4. **Stable Selectors**: Use stable selectors (data-testid, semantic selectors)
5. **Wait Strategies**: Use appropriate waiting strategies for dynamic content

## Debugging

### View Test Report
```bash
npx playwright show-report
```

### Debug Specific Test
```bash
npx playwright test --debug e2e/tests/projects/project-sessions.spec.js
```

### Record Test Actions
```bash
npx playwright codegen http://localhost:8000
```

## Adding New Tests

When adding new E2E tests:

1. Create test files in the appropriate `e2e/tests/` subdirectory
2. Follow the naming convention: `*.spec.js`
3. Use the test helpers for data management
4. Include setup and cleanup in your tests
5. Test both positive and negative scenarios
6. Consider responsive design and accessibility

## Troubleshooting

### Common Issues

1. **Server not starting**: Ensure PHP and Laravel dependencies are installed
2. **Authentication failing**: Check test credentials in test-data.js
3. **Tests timing out**: Increase timeout values in playwright.config.js
4. **Database issues**: Ensure test database is set up with appropriate data

### Getting Help

- Check the Playwright documentation: https://playwright.dev/
- Review existing test files for patterns
- Use the `--debug` flag to step through failing tests