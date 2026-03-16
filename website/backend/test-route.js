const { pathToRegexp } = require('path-to-regexp');
['/api/documents/:path(.*)', '/api/documents/*path', '/api/documents/*', '/api/documents/{*path}', '/api/documents/(.*)', '/api/documents/:path*'].forEach(route => {
  try {
    pathToRegexp(route);
    console.log(`SUCCESS: ${route}`);
  } catch(e) {
    console.error(`ERROR for ${route}:`, e.message);
  }
});
