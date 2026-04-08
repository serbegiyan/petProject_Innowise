#!/bin/bash
awslocal s3 mb s3://catalog-exports
awslocal ses verify-email-identity --email-address admin@admin.com