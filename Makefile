-include .env
CONTEXT_FILES = .env vendor
PHPTREE_VERSION ?= phptree

configure: .env

clear:
	rm -vr $(CONTEXT_FILES)

clone: vendor

vendor:
	container_id=$(shell docker create $(PHPTREE_VERSION))
	docker cp ${container_id}:$(WORKDIR)/vendor ./

.env:
	cp .env.example .env
