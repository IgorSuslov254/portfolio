<?php
namespace bigQuery;

/**
 * Interface defining the main methods of the class
 */
interface BigQueryInterfase
{
    /**
     * Retrieves the requested data object
     * @param string $typeEntity = type of entity, $chema = scheme in GBQ, $tabel = table name in GBQ
     * @return array
     */
    public function getList(string $typeEntity, string $chema, string $tabel):array;

    /**
     * Converts data to GBQ format
     * @param object $data = data to be converted to GBQ format
     * @return array
     */
    public function dataCastomization(object $data):array;
}