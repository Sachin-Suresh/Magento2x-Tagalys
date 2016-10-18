<?php
namespace Tagalys\Tglssearch\Model;

use Magento\Framework\Event\ObserverInterface;

class Observer implements ObserverInterface
{

    /**
     * @var \Tagalys\Tglssearch\Model\EngineFactory
     */
    protected $tglssearchEngineFactory;


    public function __construct(
        \Tagalys\Tglssearch\Model\EngineFactory $tglssearchEngineFactory

    ) {
        $this->tglssearchEngineFactory = $tglssearchEngineFactory;

    }
     public function createSearchObject(\Magento\Framework\Event\Observer $observer)
	{
		echo 'obssr';die;
		$this->tglssearchEngineFactory->create()->getCatalogResult();

	}

	/*The “method” parameter has been deleted in events.xml and now observers represent something like a controller (that has an additional “execute” function).*/
	/* public function createSearchResultPage(\Magento\Framework\Event\Observer $observer)
	{
			$this->tglssearchEngineFactory->create()->getCatlogSearchResult();
	} */

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		//echo 'obssrexe';die;
		$this->tglssearchEngineFactory->create()->getCatlogSearchResult();
	}

}
