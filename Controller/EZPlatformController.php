<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain, Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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