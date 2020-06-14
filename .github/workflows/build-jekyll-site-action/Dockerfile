FROM ruby:2.7-alpine

RUN apk add --no-cache git build-base libffi-dev

RUN bundle version

COPY entrypoint.sh /

ENTRYPOINT ["/entrypoint.sh"]
