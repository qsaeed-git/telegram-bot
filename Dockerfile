# استفاده از PHP 8.2
FROM php:8.2-apache

# نصب افزونه‌های لازم
RUN docker-php-ext-install pdo pdo_mysql curl

# فعال‌سازی mod_rewrite برای فایل .htaccess
RUN a2enmod rewrite

# کپی کل پروژه به داخل کانتینر
COPY . /var/www/html/

# تنظیم پوشه‌ی کاری
WORKDIR /var/www/html/

# باز کردن پورت 80 برای رندر
EXPOSE 80

# اجرای سرور Apache
CMD ["apache2-foreground"]
