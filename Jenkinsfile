pipeline {
    agent any

    options {
        timestamps()
        timeout(time: 30, unit: 'MINUTES')
        buildDiscarder(logRotator(numToKeepStr: '20'))
    }

    environment {
        PHP_CONTAINER = 'php_fpm_petProject'
        MYSQL_CONTAINER = 'mysql_db_petProject'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                script {
                    env.REPO_ROOT = fileExists("${env.WORKSPACE}/docker-compose.yml")
                        ? env.WORKSPACE
                        : "${env.WORKSPACE}/.."
                }
            }
        }

        stage('Start infrastructure') {
            steps {
                dir("${env.REPO_ROOT}") {
                    sh '''
                        set -e
                        docker compose up -d mysql_db redis rabbitmq localstack php

                        echo "Waiting for MySQL..."
                        for i in $(seq 1 30); do
                            if docker exec ${MYSQL_CONTAINER} mysqladmin ping -h localhost -uuser -ppassword --silent; then
                                break
                            fi
                            sleep 2
                        done

                        echo "Waiting for PHP container..."
                        for i in $(seq 1 30); do
                            if docker exec ${PHP_CONTAINER} php -v >/dev/null 2>&1; then
                                break
                            fi
                            sleep 2
                        done
                    '''
                }
            }
        }

        stage('Install PHP dependencies') {
            steps {
                dir("${env.REPO_ROOT}") {
                    sh '''
                        set -e
                        docker exec ${PHP_CONTAINER} composer install \
                            --no-interaction \
                            --prefer-dist \
                            --no-progress
                        docker exec ${PHP_CONTAINER} php artisan config:clear --ansi
                    '''
                }
            }
        }

        stage('Build frontend') {
            steps {
                dir("${env.REPO_ROOT}") {
                    sh '''
                        set -e
                        docker compose run --rm --no-deps --entrypoint "" node \
                            sh -c "npm ci && npm run build"
                    '''
                }
            }
        }

        stage('Analysis') {
            parallel {
                stage('Pint (code style)') {
                    steps {
                        dir("${env.REPO_ROOT}") {
                            sh "docker exec ${PHP_CONTAINER} composer lint"
                        }
                    }
                }

                stage('PHPStan') {
                    steps {
                        dir("${env.REPO_ROOT}") {
                            sh "docker exec ${PHP_CONTAINER} composer analyse"
                        }
                    }
                }
            }
        }

        stage('Tests') {
            steps {
                dir("${env.REPO_ROOT}") {
                    sh '''
                        set -e
                        docker exec ${PHP_CONTAINER} mkdir -p tests/_reports
                        docker exec ${PHP_CONTAINER} composer test -- \
                            --log-junit tests/_reports/junit.xml

                        mkdir -p "${WORKSPACE}/ci-reports"
                        docker cp \
                            ${PHP_CONTAINER}:/var/www/html/tests/_reports/junit.xml \
                            "${WORKSPACE}/ci-reports/junit.xml"
                    '''
                }
            }
        }
    }

    post {
        always {
            junit allowEmptyResults: true, testResults: 'ci-reports/*.xml'
        }
        success {
            echo 'Pipeline finished successfully.'
        }
        failure {
            echo 'Pipeline failed. Check stage logs above.'
        }
    }
}
