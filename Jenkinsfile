pipeline {
    agent {
        node {
            label 'master-executor'
        }
    }

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
                        docker exec ${PHP_CONTAINER} sh -c "grep -q '^TELESCOPE_ENABLED=' .env || echo '\\nTELESCOPE_ENABLED=false' >> .env"
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
            // Публикация результатов тестов в интерфейс Дженкинса
            junit allowEmptyResults: true, testResults: 'ci-reports/*.xml'
        }
        success {
            echo 'Pipeline finished successfully.'
            // Отправляем зеленую метку в GitHub
            repoStatus('SUCCESS', 'All tests passed!')
        }
        failure {
            echo 'Pipeline failed. Check stage logs above.'
            // Отправляем красный крестик в GitHub
            repoStatus('FAILURE', 'Pipeline failed. Check Jenkins logs.')
        }
    }
}

def repoStatus(String state, String message) {
    withCredentials([string(credentialsId: 'github_token', variable: 'TOKEN')]) {
        sh """
            curl -X POST \
              -H "Authorization: token ${TOKEN}" \
              -H "Accept: application/vnd.github.v3+json" \
              https://api.github.com/repos/serbegiyan/petProject_Innowise/statuses/${env.GIT_COMMIT} \
              -d '{"state": "${state.toLowerCase()}", "target_url": "${env.BUILD_URL}", "description": "${message}", "context": "Jenkins CI/CD"}'
        """
    }
}
