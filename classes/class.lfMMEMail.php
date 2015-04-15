<?php

/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Mail main menu entries
 *
 * @author Alex Killing <alex.killing@gmx.de>
 */
class lfMMEMail extends lfMainMenuEntryProvider
{
	const MAIL = "mail";
	const CONTACTS = "contacts";

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceTitle()
	{
		return $this->lng->txt("mail");
	}

	/**
	 * Get service title
	 *
	 * @return string service title
	 */
	public function getServiceId()
	{
		return "mail";
	}

	/**
	 * Get feature title
	 *
	 * @return string feature title
	 */
	public function getFeatures()
	{
		return array(
			self::MAIL => $this->lng->txt("mail"),
			self::CONTACTS => $this->lng->txt("mail_addressbook")
		);
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

		include_once 'Services/Mail/classes/class.ilMailGlobalServices.php';

		switch ($a_id)
		{
			case self::MAIL:
				return ($ilUser->getId() != ANONYMOUS_USER_ID &&
					$rbacsystem->checkAccess('internal_mail', ilMailGlobalServices::getMailObjectRefId()));
				break;

			case self::CONTACTS:
				return !$ilSetting->get('disable_contacts') &&
					($ilSetting->get('disable_contacts_require_mail') ||
					$rbacsystem->checkAccess('internal_mail', ilMailGlobalServices::getMailObjectRefId()));
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
			case self::MAIL:
				return $this->ctrl->getLinkTargetByClass("ilmailgui", "");
				break;

			case self::CONTACTS:
				return $this->ctrl->getLinkTargetByClass("ilpersonaldesktopgui", "jumpToContacts");
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
			case self::MAIL:
				return "mm_pd_mail";
				break;

			case self::MAIL:
				return "mm_pd_contacts";
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
		switch ($a_id)
		{
			case self::MAIL:
				return ilHelp::getMainMenuTooltip("mm_pd_mail");
				break;

			case self::CONTACTS:
				return ilHelp::getMainMenuTooltip("mm_pd_contacts");
				break;
		}
		return "";
	}

}

?>