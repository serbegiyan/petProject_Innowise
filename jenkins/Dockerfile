FROM jenkins/jenkins:lts

USER root

RUN apt-get update && \
    apt-get install -y docker.io && \
    ln -s /usr/bin/docker /usr/local/bin/docker

RUN groupadd -g 984 docker || true
RUN usermod -aG docker jenkins

USER jenkins
