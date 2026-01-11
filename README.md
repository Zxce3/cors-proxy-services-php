# CORS Proxy Service

This is a simple PHP-based CORS proxy service running on Nginx. It allows web applications to bypass Cross-Origin Resource Sharing (CORS) restrictions by proxying requests.

## Setup

1. **Prerequisites**: Ensure you have Docker and Docker Compose installed.
2. **Start the service**:

    ```bash
    docker-compose up -d
    ```

The service will be available at `http://localhost:8080`.

## Usage

To use the proxy, append the target URL to the `url` query parameter:

```plain
http://localhost:8080/?url=https://example.com/api/data.json
```

### Example Fetch Request

```javascript
const proxyUrl = 'http://localhost:8080/?url=';
const targetUrl = 'https://example.com/api/data.json';

fetch(proxyUrl + encodeURIComponent(targetUrl))
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));
```

## Security

By default, the proxy allows requests from any origin (`*`). To restrict access to specific domains, edit `public/index.php` and update the `$allowedOrigins` array:

```php
$allowedOrigins = ['https://your-app.com', 'http://localhost:3000'];
```
