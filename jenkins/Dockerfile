FROM jenkins/jenkins:lts

USER root

RUN apt-get update && \
    apt-get install -y docker.io docker-compose-plugin && \
    ln -sf /usr/bin/docker /usr/local/bin/docker && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN groupadd -g 984 docker || true
RUN usermod -aG docker jenkins

USER jenkins
