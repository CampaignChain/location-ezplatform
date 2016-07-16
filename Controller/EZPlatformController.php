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

namespace CampaignChain\Location\EZPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EZPlatformController extends Controller
{
    public function apiContentByClassAction(Request $request, $id)
    {
        $idParts = explode('-', $id);
        $locationId = $idParts[0];
        $contentTypeId = $idParts[1];

        $location = $this->getDoctrine()
            ->getRepository('CampaignChainCoreBundle:Location')
            ->find($locationId);

        if (!$location) {
            throw new \Exception(
                'No channel found for id '.$locationId
            );
        }

        $restClient = $this->get('campaignchain.channel.ezplatform.rest.client');
        $connection = $restClient->connectByLocation($location);
        $remoteContentObjects =
            $connection->getUnpublishedContentObjectsByContentTypeId($contentTypeId);

        $response = array();

        foreach($remoteContentObjects as $remoteContentObject){
            $response[] = array(
                'id' => $remoteContentObject['value']['Content']['_id'],
                'display_name' => $remoteContentObject['value']['Content']['Name'],
                'name' => $remoteContentObject['value']['Content']['_id'],
            );
        }

        $serializer = $this->get('campaignchain.core.serializer.default');

        return new Response($serializer->serialize($response, 'json'));
    }
}