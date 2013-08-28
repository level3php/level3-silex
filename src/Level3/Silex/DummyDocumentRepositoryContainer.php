<?php

namespace Level3\Silex;

class DummyDocumentRepositoryContainer implements DocumentRepositoryContainer
{
    public function getRepositoryForResource($className) {
        throw new \RuntimeException('Not Implemented. Please replace \'level3.document_repository_container\' with your own definition which, given a class name, returns a repository for it.');
    }
}