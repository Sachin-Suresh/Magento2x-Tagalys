<?php
namespace Tagalys\CatalogSearch\Observer;

use \Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class TagalysObserver implements ObserverInterface
{

    /**
     * @var \Tagalys\Tglssearch\Model\EngineFactory
     */
    protected $tglssearchEngineFactory;


    public function __construct(
        \Tagalys\CatalogSearch\Model\Engine $tglssearchEngineFactory

    ) {
        $this->tglssearchEngineFactory = $tglssearchEngineFactory;

    }
	
   /*  public function createSearchObject(\Magento\Framework\Event\Observer $observer)
	{
		echo 'obssr';die;
		$this->tglssearchEngineFactory->create()->getCatalogResult();
	} 
 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		//echo 'obssrexe';die;
		$event = $observer->getEvent();
		$this->tglssearchEngineFactory->getCatlogSearchResult();
	}

}
