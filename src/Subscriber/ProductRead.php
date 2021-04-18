<?php


namespace CustomNavigation\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class ProductRead{
    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    public function __construct(EntityRepositoryInterface $productRepository){
        $this->productRepository = $productRepository;
    }

    public function readData(Context $context, Criteria $criteria){
        return $this->productRepository->search($criteria, $context);
    }
}