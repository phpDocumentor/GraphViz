FROM php:8.2

RUN apt-get update \
    && apt-get install -y graphviz \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /opt/project
