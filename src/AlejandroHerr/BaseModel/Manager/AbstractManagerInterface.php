<?php

namespace AlejandroHerr\BaseModel\Manager;

interface AbstractManagerInterface
{
    public function deleteResource($id);
    public function getCollection($query);
    public function getCount($condition);
    public function getResourceById($id);
    public function postResource($resource);
    public function updateResource($resource);
}
