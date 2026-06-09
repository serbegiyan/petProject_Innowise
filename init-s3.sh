#!/bin/bash

# 1. Проверка и создание бакета S3
if ! awslocal s3api head-bucket --bucket catalog-exports 2>/dev/null; then
    awslocal s3 mb s3://catalog-exports
    echo "Бакет catalog-exports успешно создан."
else
    echo "Бакет catalog-exports уже существует."
fi

# 2. Верификация отправителя в SES (admin@admin.com)
if ! awslocal ses get-identity-verification-attributes --identities admin@admin.com | grep -q "Success"; then
    awslocal ses verify-email-identity --email-address admin@admin.com
    echo "Email отправителя admin@admin.com верифицирован."
fi

# 3. Верифицирия домена целиком
awslocal ses verify-domain-identity --domain admin.com
awslocal ses verify-domain-identity --domain localhost

echo "Инициализация LocalStack завершена успешно!"
