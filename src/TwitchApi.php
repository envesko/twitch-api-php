<?php

declare(strict_types=1);

namespace TwitchApi;

use GuzzleHttp\Client;
use TwitchApi\Auth\OauthApi;
use TwitchApi\Resources\AdsApi;
use TwitchApi\Resources\AnalyticsApi;
use TwitchApi\Resources\BitsApi;
use TwitchApi\Resources\ChannelPointsApi;
use TwitchApi\Resources\ChannelsApi;
use TwitchApi\Resources\CharityApi;
use TwitchApi\Resources\ChatApi;
use TwitchApi\Resources\ClipsApi;
use TwitchApi\Resources\ConduitsApi;
use TwitchApi\Resources\ContentClassificationLabelsApi;
use TwitchApi\Resources\EntitlementsApi;
use TwitchApi\Resources\EventSubApi;
use TwitchApi\Resources\ExtensionsApi;
use TwitchApi\Resources\GamesApi;
use TwitchApi\Resources\GoalsApi;
use TwitchApi\Resources\GuestStarApi;
use TwitchApi\Resources\HypeTrainApi;
use TwitchApi\Resources\ModerationApi;
use TwitchApi\Resources\PollsApi;
use TwitchApi\Resources\PredictionsApi;
use TwitchApi\Resources\RaidsApi;
use TwitchApi\Resources\ScheduleApi;
use TwitchApi\Resources\SearchApi;
use TwitchApi\Resources\SharedChatApi;
use TwitchApi\Resources\StreamsApi;
use TwitchApi\Resources\SubscriptionsApi;
use TwitchApi\Resources\TagsApi;
use TwitchApi\Resources\TeamsApi;
use TwitchApi\Resources\UsersApi;
use TwitchApi\Resources\VideosApi;
use TwitchApi\Resources\WhispersApi;

class TwitchApi
{
    private HelixGuzzleClient $helixGuzzleClient;
    private string $clientId;
    private string $clientSecret;
    private ?Client $authGuzzleClient = null;
    private ?RequestGenerator $requestGenerator = null;

    private ?OauthApi $oauthApi = null;
    private ?AdsApi $adsApi = null;
    private ?AnalyticsApi $analyticsApi = null;
    private ?BitsApi $bitsApi = null;
    private ?ChannelPointsApi $channelPointsApi = null;
    private ?ChannelsApi $channelsApi = null;
    private ?CharityApi $charityApi = null;
    private ?ChatApi $chatApi = null;
    private ?ClipsApi $clipsApi = null;
    private ?EntitlementsApi $entitlementsApi = null;
    private ?EventSubApi $eventSubApi = null;
    private ?GamesApi $gamesApi = null;
    private ?GoalsApi $goalsApi = null;
    private ?HypeTrainApi $hypeTrainApi = null;
    private ?ModerationApi $moderationApi = null;
    private ?PollsApi $pollsApi = null;
    private ?PredictionsApi $predictionsApi = null;
    private ?RaidsApi $raidsApi = null;
    private ?ScheduleApi $scheduleApi = null;
    private ?SearchApi $searchApi = null;
    private ?StreamsApi $streamsApi = null;
    private ?SubscriptionsApi $subscriptionsApi = null;
    private ?TagsApi $tagsApi = null;
    private ?TeamsApi $teamsApi = null;
    private ?UsersApi $usersApi = null;
    private ?VideosApi $videosApi = null;
    private ?WhispersApi $whispersApi = null;
    private ?ConduitsApi $conduitsApi = null;
    private ?ContentClassificationLabelsApi $contentClassificationLabelsApi = null;
    private ?ExtensionsApi $extensionsApi = null;
    private ?GuestStarApi $guestStarApi = null;
    private ?SharedChatApi $sharedChatApi = null;

    public function __construct(HelixGuzzleClient $helixGuzzleClient, string $clientId, string $clientSecret, ?Client $authGuzzleClient = null)
    {
        $this->helixGuzzleClient = $helixGuzzleClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->authGuzzleClient = $authGuzzleClient;
    }

    private function requestGenerator(): RequestGenerator
    {
        return $this->requestGenerator ??= new RequestGenerator();
    }

    public function getOauthApi(): OauthApi
    {
        return $this->oauthApi ??= new OauthApi($this->clientId, $this->clientSecret, $this->authGuzzleClient);
    }

    public function getAdsApi(): AdsApi
    {
        return $this->adsApi ??= new AdsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getAnalyticsApi(): AnalyticsApi
    {
        return $this->analyticsApi ??= new AnalyticsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getBitsApi(): BitsApi
    {
        return $this->bitsApi ??= new BitsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getChannelPointsApi(): ChannelPointsApi
    {
        return $this->channelPointsApi ??= new ChannelPointsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getChannelsApi(): ChannelsApi
    {
        return $this->channelsApi ??= new ChannelsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getCharityApi(): CharityApi
    {
        return $this->charityApi ??= new CharityApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getChatApi(): ChatApi
    {
        return $this->chatApi ??= new ChatApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getClipsApi(): ClipsApi
    {
        return $this->clipsApi ??= new ClipsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getEntitlementsApi(): EntitlementsApi
    {
        return $this->entitlementsApi ??= new EntitlementsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getEventSubApi(): EventSubApi
    {
        return $this->eventSubApi ??= new EventSubApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getGamesApi(): GamesApi
    {
        return $this->gamesApi ??= new GamesApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getGoalsApi(): GoalsApi
    {
        return $this->goalsApi ??= new GoalsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getHypeTrainApi(): HypeTrainApi
    {
        return $this->hypeTrainApi ??= new HypeTrainApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getModerationApi(): ModerationApi
    {
        return $this->moderationApi ??= new ModerationApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getPollsApi(): PollsApi
    {
        return $this->pollsApi ??= new PollsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getPredictionsApi(): PredictionsApi
    {
        return $this->predictionsApi ??= new PredictionsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getRaidsApi(): RaidsApi
    {
        return $this->raidsApi ??= new RaidsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getScheduleApi(): ScheduleApi
    {
        return $this->scheduleApi ??= new ScheduleApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getSearchApi(): SearchApi
    {
        return $this->searchApi ??= new SearchApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getStreamsApi(): StreamsApi
    {
        return $this->streamsApi ??= new StreamsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getSubscriptionsApi(): SubscriptionsApi
    {
        return $this->subscriptionsApi ??= new SubscriptionsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getTagsApi(): TagsApi
    {
        return $this->tagsApi ??= new TagsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getTeamsApi(): TeamsApi
    {
        return $this->teamsApi ??= new TeamsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getUsersApi(): UsersApi
    {
        return $this->usersApi ??= new UsersApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getVideosApi(): VideosApi
    {
        return $this->videosApi ??= new VideosApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getWhispersApi(): WhispersApi
    {
        return $this->whispersApi ??= new WhispersApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getConduitsApi(): ConduitsApi
    {
        return $this->conduitsApi ??= new ConduitsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getContentClassificationLabelsApi(): ContentClassificationLabelsApi
    {
        return $this->contentClassificationLabelsApi ??= new ContentClassificationLabelsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getExtensionsApi(): ExtensionsApi
    {
        return $this->extensionsApi ??= new ExtensionsApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getGuestStarApi(): GuestStarApi
    {
        return $this->guestStarApi ??= new GuestStarApi($this->helixGuzzleClient, $this->requestGenerator());
    }

    public function getSharedChatApi(): SharedChatApi
    {
        return $this->sharedChatApi ??= new SharedChatApi($this->helixGuzzleClient, $this->requestGenerator());
    }
}
