module.exports = {
  testEnvironment: 'node',
  testMatch: [
    '**/resources/js/tests/**/*.test.js',
    '**/resources/js/tests/**/*.spec.js'
  ],
  testPathIgnorePatterns: [
    '/node_modules/',
    '/e2e/',
    '/test-results/',
    '/playwright-report/'
  ],
  moduleFileExtensions: ['js'],
  transform: {
    '^.+\\.js$': 'babel-jest'
  }
};
