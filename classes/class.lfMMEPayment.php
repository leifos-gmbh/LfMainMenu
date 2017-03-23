<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Payment main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMEPayment extends lfMainMenuEntryProvider
{
	const SHOP = "shop";
	const CART = "cart";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("payment_system");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "paym";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		if (is_dir("./Services/Payment"))	// only in ILIAS < 5.1
		{
			return array(
				self::SHOP => $this->lng->txt("shop"),
				self::CART => $this->lng->txt("shoppingcart")
			);
		}
		return array();
	}

	/**
	 * Check, if the entry is visible
	 *
	 * @param string feature id
	 * @return bool active
	 */
	public function isVisible($a_id)
	{
		global $rbacsystem, $ilUser, $ilSetting;

		switch ($a_id)
		{
			case self::SHOP:
				return IS_PAYMENT_ENABLED;
				break;

			case self::CART:
				$cart = false;
				if (IS_PAYMENT_ENABLED)
				{
					include_once 'Services/Payment/classes/class.ilPaymentShoppingCart.php';
					global $ilUser;
					$objShoppingCart = new ilPaymentShoppingCart($ilUser);
					$items = $objShoppingCart->getEntries();
					if(count($items) > 0 )
					{
						$cart = true;
					}
				}
				return $cart;
				break;
		}
		return true;
	}

	/**
	 * Get content
	 *
	 * @param string feature id
	 * @return string href
	 */
	public function getHref($a_id)
	{
		switch ($a_id)
		{
			case self::SHOP:
				return $this->ctrl->getLinkTargetByClass("ilshopcontroller", "firstpage");
				break;

			case self::CART:
				return $this->ctrl->getLinkTargetByClass(array("ilshopcontroller", "ilshopshoppingcartgui"), "");
				break;
		}
		return "";
	}

	/**
	 * Get id
	 *
	 * @param string feature id
	 * @return string element id
	 */
	public function getDomElementId($a_id)
	{
		switch ($a_id)
		{
			case self::SHOP:
				return "";
				break;

			case self::CART:
				return "";
				break;
		}
		return "";
	}

	/**
	 * Get tooltip
	 *
	 * @param string feature id
	 * @return string tooltip content
	 */
	public function getTooltip($a_id)
	{
		return "";
	}

}

?>