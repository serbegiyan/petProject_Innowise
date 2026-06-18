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
        NODE_CONTAINER = 'node_petProject'
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

                        start_container() {
                            if ! docker inspect "$1" >/dev/null 2>&1; then
                                echo "Container $1 not found."
                                echo "Create the stack once from the project root:"
                                echo "  docker compose up -d"
                                exit 1
                            fi
                            docker start "$1" >/dev/null
                        }

                        start_container ${MYSQL_CONTAINER}
                        start_container redis_petProject
                        start_container rabbitmq
                        start_container localstack_s3
                        start_container ${PHP_CONTAINER}

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
                        docker start ${NODE_CONTAINER} >/dev/null
                        docker exec ${NODE_CONTAINER} sh -c "npm ci && npm run build"
                    '''
                }
            }
        }

        stage('Analysis') {
            parallel {
                stage('Pint (code style)') {
                    steps {
                        dir("${env.REPO_ROOT}") {
                            sh "docker exec ${env.PHP_CONTAINER} composer lint"
                        }
                    }
                }

                stage('PHPStan') {
                    steps {
                        dir("${env.REPO_ROOT}") {
                            sh "docker exec ${env.PHP_CONTAINER} composer analyse"
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
                        docker exec ${PHP_CONTAINER} composer test:ci

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
