server {
    listen       80;
    server_name ldev.admin-permission.putao.com ldev.permission.putao.com;
    #access_log /var/log/nginx/admin-planet.access.log;
    #error_log /var/log/nginx/admin-planet.error.log;
    #root /Users/allen/community/webstore/admin.community.putao.com/laravel/public/;

	location / {
		proxy_pass http://127.0.0.1:8200;
		#proxy_http_version 1.1;
		proxy_set_header X-Real-IP $remote_addr;
		#proxy_set_header Upgrade $http_upgrade;
		proxy_set_header Connection 'upgrade';
		proxy_set_header X-Forwarded-For $remote_addr;
		proxy_set_header Host $host;
	}
	
	location ~ ^/(js|css|images|static)/ {

		root /Users/sunjunjie/work/permission/public/;
		#expires 30d;
	}

    location ~ ^/favicon\.ico$ {
          root /Users/sunjunjie/work/permission/public/images;
    }
}

