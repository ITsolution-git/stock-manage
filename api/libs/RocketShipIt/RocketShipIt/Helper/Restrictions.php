<?php

namespace RocketShipIt\Helper;

/**
 * Restrictions
 *
 * @package 
 * @author RocketShipIt
 */
class Restrictions {

    public $restrictions = array(
        'A' => 'Securities, currency, or precious metals in their raw, unmanufactured state are prohibited. Official shipments are exempt from this restriction.',
        'A1' => 'Mail addressed to “Any Servicemember,” or similar wording such as “Any Soldier,” “Sailor,” “Airman,” or “Marine”; “Military Mail”; etc., is prohibited. Mail must be addressed to an individual or job title such as “Commander,” “Commanding Officer,” etc.',
        'A2' => 'APO/FPO/DPO addresses shall not include a city and/or country name.',
        'B' => 'Regardless of mail class, a customs declaration (PS Form 2976 or, if the customer prefers, PS Form 2976-A) is required for all items weighing 16 ounces or more or any item (regardless of weight) containing potentially dutiable mail contents (e.g., merchandise or goods) addressed to or from an APO, FPO, or DPO ZIP Code. No customs form is required for items weighing less than 16 ounces when the contents are not potentially dutiable (e.g., documents). The surface area of the address side of the mailpiece must be large enough to contain the applicable customs declaration. The following exceptions apply:
        n Known mailers are exempt from providing customs documentation on non-dutiable letters or printed matter. (A known mailer is a business mailer who enters volume mailings through a business mail entry unit (BMEU) or other bulk mail acceptance location, pays postage through an advance deposit account, uses a permit imprint for postage payment, and submits a completed postage statement at the time of entry that certifies that the mailpieces contain no dangerous materials that are prohibited by postal regulations.)
        n All federal, state, and local government agencies whose mailings are regarded as “Official Mail” are exempt from providing customs documentation on any item addressed to an APO, FPO, or DPO except for those APOs/FPOs/DPOs to which restriction "B2" applies.
       n Prepaid mail from military contractors is exempt, providing the mailpiece is endorsed “Contents for Official Use — Exempt from Customs Requirements.”', 
       'B2' => 'All federal, state, and local government agencies must complete customs documentation when sending potentially dutiable mail addressed to or from this APO, FPO, or DPO.',
       'C' => 'Cigarettes and other tobacco products are prohibited.',
       'C1' => 'Obscene articles, prints, paintings, cards, films, videotapes, etc., and horror comics and matrices are prohibited.',
       'D' => 'Coffee is prohibited.',
       'E' => 'Medicines (prescription, over-the-counter, vitamins, and supplements) are prohibited when mailed to individuals for human or animal use. This prohibition does not apply when medicines are sent as official mail only between specifically designated agencies such as pharmaceutical distributors, hospitals, clinics, and pharmacies.',
       'E1' => 'Medicines or vaccines not conforming to French laws are prohibited.',
       'E2' => 'Any matter depicting nude or seminude persons, pornographic or sexual items, or nonauthorized political materials is prohibited. Although religious materials contrary to the Islamic faith are prohibited in bulk quantities, items for the personal use of the addressee are permissible.',
       'E3' => 'Radio transceivers, cordless telephones, global positioning systems, scanners, base stations, and handheld transmitters are prohibited.',
       'F' => 'Firearms of any type are prohibited in all classes of mail. See definitions of firearms in DMM 601.12.1.1. This restriction does not apply to firearms mailed to or by official U.S. government agencies. The restriction for mail to this APO/FPO/DPO ZIP Code does not apply to firearms mailed from this APO/FPO/DPO ZIP Code, provided ATF and USPS regulations are met. Antique firearms are a separate category defined in DMM 601.12.1.1h and ATF regulations; they do not require an ATF form.',
       'F1' => 'Privately owned weapons addressed to an individual are prohibited in any class of mail.',
       'F2' => 'Importation of firearms is restricted to one shotgun and one single shot.22 caliber rifle per individual.',
       'G' => 'Only letters, flats, and Periodicals are authorized. Parcels of any class are prohibited.',
       'H' => 'Meats, including preserved meats, whether hermetically sealed or not, are prohibited.',
       'H1' => 'Pork or pork by-products are prohibited.',
       'I' => 'Mail of all classes must fit in a mail sack. Mail may not exceed the following dimensions:
       n Maximum length 20 inches.
       n Maximum width 12 inches.
       n Maximum height 12 inches.
       The maximum length and girth combined may not exceed 68 inches.
       This restriction does not apply to registered mail and official government mail marked MOM.',
       'I1' => 'This restriction does not apply to registered mail.',
       'I2' => 'This restriction does not apply to official government mail marked MOM.',
       'I3' => 'Mail may not exceed the following dimensions:
       * Maximum length 27 inches
       * Maximum width 14 inches 
       * Maximum height 14 inches',
       'J' => 'Parcels may not exceed 108 inches in length and girth combined.',
       'K' => 'Mail that includes in the address the words, “Dependent Mail Section,” may consist only of letter mail, newspapers, magazines, and books. No parcel of any class containing any other matter may be mailed to the Dependent Mail section. This restriction does not apply if the address does not include the words “Dependent Mail Section.”',
       'L' => 'All official mail is prohibited.',
       'M' => 'Fruits, vegetables, animals, and living plants are prohibited.',
       'N' => 'Registered mail is prohibited.',
       'O' => 'Delivery status information for Extra Services is not available on USPS.com.',
       'P' => 'APO is used for the receipt and dispatch of official mail only.',
       'Q' => 'Mail may not exceed 66 pounds, and size is limited to 42 inches maximum length and 72 inches maximum length and girth combined.',
       'R' => 'All alcoholic beverages, including those mailable under DMM 601.12.7, are prohibited.',
       'R1' => 'Materials used in the production of alcoholic beverages (i.e., distilling material, hops, malts, yeast, etc.) are prohibited.',
       'S' => 'Mail of all classes must fit in a mail sack. Mail may not exceed the following dimensions and weight:
       n Maximum length 12 inches.
       n Maximum width 12 inches.
       n Maximum height 5 1/2 inches.
       n Maximum weight 25 pounds.
       The maximum length and girth combined may not exceed 47 inches.',
       'T' => 'Mailings of case lots of food and supplemental household shipments must be approved by the sender’s parent agency prior to mailing.',
       'U' => 'Parcels must weigh less than 16 ounces when addressed to Box R. This restriction does not apply to mail endorsed “Free Matter for the Blind or Handicapped.”',
       'U1' => 'Mail is limited to First-Class Mail weighing 13 ounces or less when addressed to Box R. This restriction does not apply to mail endorsed “Free Matter for the Blind or Handicapped.” Videotapes are prohibited when addressed to Box R, regardless of weight.',
       'U2' => 'Mail is limited to First-Class Mail letters only when addressed to Box R.',
       'U3' => 'Mail is limited to First-Class Mail correspondence (including voice and video cassettes), newspapers, magazines, photographs, not exceeding 16 ounces, when addressed to Box R.',
       'U4' => 'Mail addressed to Box C is limited to 2 pounds, regardless of class.',
       'V' => 'Express Mail Military Service (EMMS) not available from any origin.',
       'V1' => 'USPS Tracking is not available.',
       'W' => 'Meat products, such as dried beef, salami, and sausage, may be mailed, provided they remain in their original, hermetically sealed packages and bear USDA certification. Other meats, bones, skin, hair, feathers, horns or hoofs of hoofed animals, wool samples, tobacco leaves, including chewing and pipe tobacco, snuff, cigars, and cigarettes, or obscene material, including obscene drawings, photographs, films, and carvings, are prohibited. Exception: 200 grams of tobacco per parcel are permitted duty free.',
       'X' => 'Personal mail is limited to First-Class Mail items (to include audio cassettes and voice tapes) weighing 13 ounces or less. This limitation does not apply to official mail.',
       'Y' => 'Mail is limited to First-Class and Priority Mail items only. All Periodicals, Standard Mail items, and Package Services items (including SAM and PAL) are not authorized. This restriction also applies to official mail.',
       'Z' => 'No outside pieces (OSPs).',
       'Z1' => 'The following restriction is applicable only to International Service Centers (ISC)/Exchange Offices. An Anti-Pilferage Seal (Item No O817E or O818A) is required on all pouches and sacks.',

    );

    public function parseRestrictions()
    {
        $restrictions = array();
        foreach ($this->getRestrictions() as $restrictionCode) {
            if (isset($this->restrictions[$restrictionCode])) {
                $restrictions[$restrictionCode] = $this->restrictions[$restrictionCode];
            }
        }

        return $restrictions;
    }

    public function getRestrictions()
    {
        if (!$this->restrictionsText) {
            return array();
        }

        $restrictions = array();
        foreach ($this->restrictions as $code => $desc) {
            if (preg_match('/(^|[\s])'. $code.'\. /', $this->restrictionsText)) {    
                $restrictions[] = $code;
            }
        }

        return $restrictions;
    }

    public function load($restrictionsText)
    {
        $this->restrictionsText = $restrictionsText;
    }
}
