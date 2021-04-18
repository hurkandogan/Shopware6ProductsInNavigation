<?php
namespace CustomNavigation\Subscriber;

use CustomNavigation\Subscriber\ProductRead;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;


use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\FilterAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Rule\Container\Container;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ProductsInNavigation implements EventSubscriberInterface{

    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public static function getSubscribedEvents(): array {
        return [
            NavigationPageLoadedEvent::class => 'onNavigationLoaded'
        ];
    }

    public function onNavigationLoaded(NavigationPageLoadedEvent $event){
        $category_tree = $event->getPage()->getHeader()->getNavigation()->getTree();
        // First category in the list is always 'Products' main category
        $productRepo = $this->container->get('product.repository');

        foreach ($category_tree as $categoryTreeItem){
            $subCategoryArray = $categoryTreeItem->getChildren();
            if($subCategoryArray){
                foreach($subCategoryArray as $subCategory){
                    $criteria = new Criteria();
                    $criteria->addFilter( new AndFilter([
                        new EqualsFilter('active', true),
                        new EqualsFilter('categoryTree', $subCategory->getCategory()->getId())
                    ]));
                    $products = (new ProductRead($productRepo))->readData($event->getContext(), $criteria)->getEntities();
                    $subCategory->getCategory()->setProducts($products);
                }
            }
//            echo'<pre>';
//            var_dump($products);
//            exit(0);
//            echo'</pre>';
        }
    }
}