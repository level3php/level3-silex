<?php

namespace Level3\Silex;


interface DocumentRepositoryContainer {
    public function getRepositoryForResource($className);
}
