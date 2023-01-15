<?php
namespace App\Service;


use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;

/**
 * Aplication Configuration Service
 *
 * Service class allows to Get configurations defined in database 
 */
class ConfigurationService 
{

    public function __construct(
        public ConfigurationRepository $repo
    ){}

    /**
     * Get a configuration
     *
     * @param string $const Name of the config
     * @return Configuration|null
     */
    public function get(string $const): ?Configuration
    {
        return $this->repo->findOneByConstNameField($const);
    }

    /**
     * Get All Configurations of one category
     *
     * @param string $category Name of the configuration category
     * @return Configuration[]|null Array of Configuration from the same category
     */
    public function getByCategory(string $category): ?array
    {
        return $this->repo->findByCategory($category);
    }
    
}