<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Location\EZPlatformBundle\Controller\REST;

use CampaignChain\CoreBundle\Controller\REST\BaseController;
use CampaignChain\CoreBundle\Entity\Location;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class LocationController extends BaseController
{
    /**
     * Get a list of eZ Platform content object Locations.
     *
     * Example Request
     * ===============
     *
     *      GET /api/{version}/p/campaignchain/location-ezplatform/contents/objects
     *
     * Example Response
     * ================
    {
        "response": [
            {
                "id": 108,
                "remoteId": "62",
                "name": "eZ Publish Tutorials",
                "status": "unpublished",
                "createdDate": "2015-12-01T09:37:39+0000",
                "operationId": "92",
                "channelId": "8"
            }
        ]
    }
     *
     * @ApiDoc(
     *  section="Packages: eZ Platform"
     * )
     */
    public function getContentsObjectsAction()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(\CampaignChain\CoreBundle\Controller\REST\LocationController::SELECT_STATEMENT);
        $qb->from('CampaignChain\CoreBundle\Entity\Location', 'l');
        $qb->where('l.channel IS NULL');
        $qb->orderBy('l.name');
        $qb = $this->getModuleRelation($qb, 'l.locationModule', 'campaignchain/location-ezplatform/campaignchain-ezplatform-object');
        $qb = $this->getLocationChannelId($qb);
        $query = $qb->getQuery();

        return $this->response(
            $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
        );
    }
}