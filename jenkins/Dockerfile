FROM jenkins/jenkins:lts

USER root

# Устанавливаем docker-cli из стандартного репозитория Debian
RUN apt-get update && \
    apt-get install -y docker.io && \
    ln -s /usr/bin/docker /usr/local/bin/docker

# Добавляем jenkins в группу docker
RUN groupadd -g 984 docker || true
RUN usermod -aG docker jenkins

USER jenkins
