ARG PHP_IMAGE
ARG PHP_ALPINE_IMAGE


FROM php:${PHP_IMAGE:-8} AS base
ARG WORKDIR
WORKDIR ${WORKDIR}


FROM base AS skeleton
COPY --from=composer/composer /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y git zip
COPY composer.json composer.lock .
COPY mk/docker.mk ./Makefile


FROM skeleton AS develop
RUN make build


FROM skeleton AS source
ARG WORKDIR
ARG ABSPATH_PHPTREE
COPY --from=develop ${WORKDIR}/vendor ./vendor
COPY build.php .
COPY bin ./bin
COPY src ./src
RUN make compile
RUN make install OUTPUT=${ABSPATH_PHPTREE}
CMD ["${ABSPATH_PHPTREE}"]


FROM base AS unit
ARG ABSPATH_PHPTREE
COPY --from=source ${ABSPATH_PHPTREE} ${ABSPATH_PHPTREE}


FROM scratch AS artifact
ARG WORKDIR
ARG ABSPATH_PHPTREE
COPY --from=source ${WORKDIR} ${WORKDIR}
COPY --from=source ${ABSPATH_PHPTREE} ${ABSPATH_PHPTREE}


FROM php:${PHP_ALPINE_IMAGE:-8-alpine3-23} AS unit-alpine
ARG ABSPATH_PHPTREE
COPY --from=source ${ABSPATH_PHPTREE} ${ABSPATH_PHPTREE}
CMD ["${ABSPATH_PHPTREE}"]
