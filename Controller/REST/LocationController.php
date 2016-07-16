<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CampaignChain\Location\EZPlatformBundle\Controller\REST;

use CampaignChain\CoreBundle\Controller\REST\BaseModuleController;
use CampaignChain\CoreBundle\Entity\Location;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Request\ParamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @REST\NamePrefix("campaignchain_location_ezplatform_rest_")
 *
 * Class LocationController
 * @package CampaignChain\Location\EZPlatformBundle\Controller\REST
 */
class LocationController extends BaseModuleController
{
    const MODULE_URI = 'campaignchain/location-ezplatform/campaignchain-ezplatform-object';

    /**
     * Get a list of eZ Platform content object Locations.
     *
     * Example Request
     * ===============
     *
     *      GET /api/{version}/p/campaignchain/location-ezplatform/objects
     *
     * Example Response
     * ================
    {
        "response": [
            {
                "id": 108,
                "identifier": "62",
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
    public function getObjectsAction()
    {
        $qb = $this->getQueryBuilder();
        $qb->select(\CampaignChain\CoreBundle\Controller\REST\LocationController::SELECT_STATEMENT);
        $qb->from('CampaignChain\CoreBundle\Entity\Location', 'l');
        $qb->where('l.channel IS NULL');
        $qb->orderBy('l.name');
        $qb = $this->getModuleRelation($qb, 'l.locationModule', self::MODULE_URI);
        $qb = $this->getLocationChannelId($qb);
        $query = $qb->getQuery();

        return $this->response(
            $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
        );
    }
}