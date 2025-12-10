<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Gateway Dashboard - Monitoring</title>
    <meta name="description" content="API Gateway monitoring dashboard for microservices architecture">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        header {
            margin-bottom: 2rem;
            border-bottom: 1px solid #334155;
            padding-bottom: 1rem;
        }
        
        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .metric-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .metric-label {
            color: #94a3b8;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #f1f5f9;
        }
        
        .metric-unit {
            font-size: 1rem;
            color: #64748b;
            margin-left: 0.25rem;
        }
        
        .section {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #f1f5f9;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .service-list {
            display: grid;
            gap: 1rem;
        }
        
        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 6px;
        }
        
        .service-name {
            font-weight: 500;
            color: #f1f5f9;
        }
        
        .service-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #10b981;
            color: white;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .service-stats {
            display: flex;
            gap: 1.5rem;
            color: #94a3b8;
            font-size: 0.875rem;
        }
        
        .log-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .log-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-bottom: 1px solid #334155;
            font-size: 0.875rem;
        }
        
        .log-item:last-child {
            border-bottom: none;
        }
        
        .log-method {
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            min-width: 60px;
            text-align: center;
        }
        
        .method-GET { background: #3b82f6; color: white; }
        .method-POST { background: #10b981; color: white; }
        .method-PUT { background: #f59e0b; color: white; }
        .method-DELETE { background: #ef4444; color: white; }
        
        .log-endpoint {
            flex: 1;
            color: #94a3b8;
            font-family: 'Courier New', monospace;
        }
        
        .log-status {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .status-2xx { background: #10b981; color: white; }
        .status-4xx { background: #f59e0b; color: white; }
        .status-5xx { background: #ef4444; color: white; }
        
        .log-time {
            color: #64748b;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        .btn:hover {
            background: #2563eb;
        }
        
        .loading {
            color: #94a3b8;
            text-align: center;
            padding: 2rem;
        }
        
        .error {
            color: #ef4444;
            background: #7f1d1d;
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🚀 API Gateway Dashboard</h1>
            <p class="subtitle">Real-time monitoring for microservices architecture</p>
        </header>
        
        <div class="metrics-grid" id="metrics">
            <div class="metric-card">
                <div class="metric-label">Total Requests</div>
                <div class="metric-value pulse">--</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Avg Response Time</div>
                <div class="metric-value pulse">--<span class="metric-unit">ms</span></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Cache Hit Rate</div>
                <div class="metric-value pulse">--<span class="metric-unit">%</span></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Success Rate</div>
                <div class="metric-value pulse">--<span class="metric-unit">%</span></div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">
                <span>📦</span>
                <span>Microservices Status</span>
            </div>
            <div class="service-list" id="services">
                <div class="loading">Loading services...</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">
                <span>📊</span>
                <span>Recent Activity</span>
                <button class="btn" onclick="loadLogs()" style="margin-left: auto; font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                    Refresh
                </button>
            </div>
            <div class="log-list" id="logs">
                <div class="loading">Loading activity logs...</div>
            </div>
        </div>
    </div>
    
    <script>
        const BASE_PATH = '<?php echo BASE_PATH; ?>';
        
        async function fetchAPI(endpoint) {
            const response = await fetch(BASE_PATH + endpoint);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        }
        
        async function loadMetrics() {
            try {
                const data = await fetchAPI('/api/metrics');
                const metricsDiv = document.getElementById('metrics');
                
                metricsDiv.innerHTML = `
                    <div class="metric-card">
                        <div class="metric-label">Total Requests</div>
                        <div class="metric-value">${data.total_requests || 0}</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Avg Response Time</div>
                        <div class="metric-value">${data.avg_response_time || 0}<span class="metric-unit">ms</span></div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Cache Hit Rate</div>
                        <div class="metric-value">${data.cache_hit_rate || 0}<span class="metric-unit">%</span></div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Success Rate</div>
                        <div class="metric-value">${data.success_rate || 100}<span class="metric-unit">%</span></div>
                    </div>
                `;
            } catch (error) {
                console.error('Error loading metrics:', error);
            }
        }
        
        async function loadServices() {
            try {
                const data = await fetchAPI('/api/stats/services');
                const servicesDiv = document.getElementById('services');
                
                if (!data.services || data.services.length === 0) {
                    servicesDiv.innerHTML = '<div class="loading">No services available</div>';
                    return;
                }
                
                servicesDiv.innerHTML = data.services.map(service => `
                    <div class="service-item">
                        <div>
                            <div class="service-name">${service.name}</div>
                            <div class="service-stats">
                                <span>Requests: ${service.requests || 0}</span>
                                <span>Avg Response: ${service.avg_response_time || 0}ms</span>
                            </div>
                        </div>
                        <span class="service-status">${service.status}</span>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading services:', error);
                document.getElementById('services').innerHTML = 
                    '<div class="error">Failed to load services</div>';
            }
        }
        
        async function loadLogs() {
            try {
                const data = await fetchAPI('/api/logs?limit=20');
                const logsDiv = document.getElementById('logs');
                
                if (!data.logs || data.logs.length === 0) {
                    logsDiv.innerHTML = '<div class="loading">No activity logs yet</div>';
                    return;
                }
                
                logsDiv.innerHTML = data.logs.map(log => {
                    const statusClass = log.status >= 500 ? 'status-5xx' : 
                                       log.status >= 400 ? 'status-4xx' : 'status-2xx';
                    const time = new Date(log.created_at).toLocaleTimeString();
                    
                    return `
                        <div class="log-item">
                            <span class="log-method method-${log.method}">${log.method}</span>
                            <span class="log-endpoint">${log.endpoint}</span>
                            <span class="log-status ${statusClass}">${log.status}</span>
                            <span class="log-time">${log.response_time_ms}ms</span>
                            <span class="log-time">${time}</span>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error('Error loading logs:', error);
                document.getElementById('logs').innerHTML = 
                    '<div class="error">Failed to load activity logs</div>';
            }
        }
        
        // Initial load
        loadMetrics();
        loadServices();
        loadLogs();
        
        // Auto-refresh every 5 seconds
        setInterval(() => {
            loadMetrics();
            loadServices();
            loadLogs();
        }, 5000);
    </script>
</body>
</html>
