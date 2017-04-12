<?php
namespace Framework\Syscrack\Game;

/**
 * Lewis Lancaster 2017
 *
 * Class Market
 *
 * @package Framework\Syscrack\Game
 */

use Framework\Application\Settings;
use Framework\Application\Utilities\FileSystem;
use Framework\Exceptions\SyscrackException;

class Market
{

    /**
     * Gets the stock items value
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getGlobalStockItemValue( $itemid )
    {

        return $this->getMarketGlobalStock()[ $itemid ]["value"];
    }

    /**
     * Gets the stock items type
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getGlobalStockItemType( $itemid )
    {

        return $this->getMarketGlobalStock()[ $itemid ]["type"];
    }

    /**
     * Gets the stock items name
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getGlobalStockItemName( $itemid )
    {

        return $this->getMarketGlobalStock()[ $itemid ]["name"];
    }

    /**
     * Gets the items data
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getGlobalStockItemData( $itemid )
    {

        return json_decode( $this->getMarketGlobalStock()[ $itemid ]["data"] );
    }

    /**
     * Gets the stock items price
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getGlobalStockItemPrice( $itemid )
    {

        return $this->getMarketGlobalStock()[ $itemid ]["price"];
    }

    /**
     * Inserts a new stock item
     *
     * @param $computerid
     *
     * @param $name
     *
     * @param $price
     *
     * @param $quantity
     *
     * @param $type
     *
     * @param array $data
     *
     * @return mixed
     */

    public function insertLocalStockItem( $computerid, $name, $price, $quantity, $type, array $data )
    {

        $stock = $this->getMarketLocalStock( $computerid );

        $array = array(
            'name'      => $name,
            'price'     => $price,
            'quantity'  => $quantity,
            'type'      => $type,
            'data'      => json_encode( $data )
        );

        array_push( $stock, $array );

        $this->save( $computerid, 'stock.json', $stock );

        return end(array_keys($stock));
    }

    /**
     * Updates the quantity
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @param $quantity
     */

    public function updateQuantity( $computerid, $itemid, $quantity )
    {

        $stock = $this->getMarketLocalStock( $computerid );

        if( isset( $stock[ $itemid ] ) == false )
        {

            throw new SyscrackException();
        }

        $stock[ $itemid ]['quantity'] = $quantity;

        $this->save( $computerid, 'stock.json', $stock );
    }

    /**
     * Updates the name
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @param $name
     */

    public function updateName( $computerid, $itemid, $name )
    {

        $stock = $this->getMarketLocalStock( $computerid );

        if( isset( $stock[ $itemid ] ) == false )
        {

            throw new SyscrackException();
        }

        $stock[ $itemid ]['quantity'] = $name;

        $this->save( $computerid, 'stock.json', $stock );
    }

    /**
     * Gets the quantity of the local stock
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getLocalStockQuantity( $computerid, $itemid )
    {

         return $this->getMarketLocalStock( $computerid )[ $itemid ]["quantity"];
    }

    /**
     * Gets this stock items price
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getLocalStockPrice( $computerid, $itemid )
    {

        return $this->getMarketLocalStock( $computerid )[ $itemid ]["price"];
    }

    /**
     * Gets this stock items name
     *
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getLocalStockName( $computerid, $itemid )
    {

        return $this->getMarketLocalStock( $computerid )[ $itemid ]["name"];
    }

    /**
     * Gets this stock item type
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getLocalStockType( $computerid, $itemid )
    {

        return $this->getMarketLocalStock( $computerid )[ $itemid ]["type"];
    }

    /**
     * Gets this stock items data
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return mixed
     */

    public function getLocalStockData( $computerid, $itemid )
    {

        return json_decode( $this->getMarketLocalStock( $computerid )[ $itemid ]["data"] );
    }

    /**
     * Returns true if the item is a software
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return bool
     */

    public function isSellingSoftware( $computerid, $itemid )
    {

        $stock = $this->getMarketLocalStock( $computerid );

        if( $stock[ $itemid ]['type'] !== Settings::getSetting('syscrack_software_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if this itemid is a hardware type
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return bool
     */

    public function isSellingHardware( $computerid, $itemid )
    {

        $stock = $this->getMarketLocalStock( $computerid );

        if( $stock[ $itemid ]['type'] !== Settings::getSetting('syscrack_hardware_type') )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if this item has data
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @param null $field
     *
     * @return bool
     */

    public function itemHasData( $computerid, $itemid, $field=null )
    {

        $stock = $this->getMarketLocalStock( $computerid );

        if( isset( $stock[ $itemid ]["data"] ) == false )
        {

            return false;
        }

        $data = $stock[ $itemid ]["data"];

        if( empty( $data ) )
        {

            return false;
        }

        if( $field != null )
        {

            $array = json_decode( $data, true );

            if( isset( $array[ $field ] ) == false )
            {

                return false;
            }

            if( empty( $array[ $field ] ) )
            {

                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if we have this stock item
     *
     * @param $itemid
     *
     * @return bool
     */

    public function hasGlobalStockItem( $itemid )
    {

        $stock = $this->getMarketGlobalStock();

        if( isset( $stock['itemid'] ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Returns true if we have this stock item
     *
     * @param $computerid
     *
     * @param $itemid
     *
     * @return bool
     */

    public function hasLocalStockItem( $computerid, $itemid )
    {

        $stock = $this->getMarketLocalStock( $computerid );

        if( isset( $stock[ $itemid ] ) == false )
        {

            return false;
        }

        return true;
    }

    /**
     * Reads our market purchases from file
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getMarketPurchases( $computerid )
    {

        return FileSystem::readJson( $this->getFilePath( $computerid ) );
    }

    /**
     * Returns true if this market has pervious purchases
     *
     * @param $computerid
     *
     * @return bool
     */

    public function hasMarketPurchases( $computerid )
    {

        if( FileSystem::fileExists( $this->getFilePath( $computerid ) ) == false )
        {

            return false;
        }

        if( empty( FileSystem::readJson( $this->getFilePath( $computerid ) ) ) )
        {

            return false;
        }

        return true;
    }

    /**
     * Gets a computers stock
     *
     * @param $computerid
     *
     * @return mixed
     */

    public function getMarketLocalStock( $computerid )
    {

        return FileSystem::readJson( $this->getFilePath( $computerid, 'stock.json') );
    }

    /**
     * Gets the market stock
     *
     * @return array|null
     */

    public function getMarketGlobalStock()
    {

        return FileSystem::readJson( $this->getFilePath() );
    }

    /**
     * Writes to file
     *
     * @param $computerid
     *
     * @param string $file
     *
     * @param array $data
     */

    private function save( $computerid, $file='purchases.json', $data=[] )
    {

        FileSystem::writeJson( $this->getFilePath( $computerid, $file ), $data );
    }

    /**
     * Returns the filepath of our stocks.json
     *
     * @return string
     */

    private function getFilePath( $computerid=null, $file='purchases.json' )
    {
        if( $computerid == null )
        {

            return Settings::getSetting('syscrack_marketstock_location');
        }

        return Settings::getSetting('syscrack_marketpurchases_location') . $computerid . "/" . $file;
    }
}