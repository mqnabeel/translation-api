<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translation API Documentation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 { color: #2c3e50; }
        h2 { 
            color: #3498db; 
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        h3 { color: #2980b9; }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .endpoint {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .method {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        .get { background-color: #61affe; }
        .post { background-color: #49cc90; }
        .put { background-color: #fca130; }
        .delete { background-color: #f93e3e; }
        .path {
            font-family: monospace;
            font-size: 16px;
        }
        .description {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Translation API Documentation</h1>
    <p>API for managing translation keys, content, and tags across multiple languages</p>
    
    <h2>Authentication</h2>
    <p>This API uses Bearer token authentication. Include your token in the Authorization header:</p>
    <pre>Authorization: Bearer your-token-here</pre>

    <h2>Endpoints</h2>
    
    <div class="endpoint">
        <span class="method get">GET</span>
        <span class="path">/api/translations</span>
        <div class="description">List all translations (paginated)</div>
        <h3>Query Parameters</h3>
        <ul>
            <li><code>page</code> - Page number (default: 1)</li>
            <li><code>per_page</code> - Items per page (default: 10)</li>
        </ul>
    </div>

    <div class="endpoint">
        <span class="method post">POST</span>
        <span class="path">/api/translations</span>
        <div class="description">Create a new translation</div>
        <h3>Request Body</h3>
<pre>{
  "key": "welcome_message",
  "content": "Welcome to our application",
  "locale": "en",
  "tags": [1, 2]
}</pre>
    </div>

    <div class="endpoint">
        <span class="method get">GET</span>
        <span class="path">/api/translations/{id}</span>
        <div class="description">Get a specific translation</div>
    </div>

    <div class="endpoint">
        <span class="method put">PUT</span>
        <span class="path">/api/translations/{id}</span>
        <div class="description">Update a translation</div>
        <h3>Request Body</h3>
<pre>{
  "content": "Updated welcome message",
  "tags": [1, 2, 3]
}</pre>
    </div>

    <div class="endpoint">
        <span class="method delete">DELETE</span>
        <span class="path">/api/translations/{id}</span>
        <div class="description">Delete a translation</div>
    </div>

    <div class="endpoint">
        <span class="method get">GET</span>
        <span class="path">/api/translations/export</span>
        <div class="description">Export translations for a specific locale</div>
        <h3>Query Parameters</h3>
        <ul>
            <li><code>locale</code> - Language locale (default: en)</li>
        </ul>
    </div>

    <div class="endpoint">
        <span class="method get">GET</span>
        <span class="path">/api/translations/search</span>
        <div class="description">Search translations by key, content or tags</div>
        <h3>Query Parameters</h3>
        <ul>
            <li><code>key</code> - Search by translation key</li>
            <li><code>content</code> - Search by translation content</li>
            <li><code>tag</code> - Search by tag ID</li>
            <li><code>locale</code> - Filter by locale</li>
            <li><code>page</code> - Page number (default: 1)</li>
        </ul>
    </div>

    <h2>Download OpenAPI Specification</h2>
    <p>
        <a href="{{ url('/openapi.yaml') }}" download>Download OpenAPI Specification (YAML)</a>
    </p>
    <p>
        You can import this file into tools like Postman or Insomnia to test the API.
    </p>
</body>
</html> 