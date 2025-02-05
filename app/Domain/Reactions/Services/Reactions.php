<?php

namespace Leantime\Domain\Reactions\Services {

    class Reactions
    {
        /**
         * @access private
         * @var    \Leantime\Domain\Reactions\Repositories\Reactions $reactionsRepo reactions repository
         */
        private \Leantime\Domain\Reactions\Repositories\Reactions $reactionsRepo;

        public function __construct(\Leantime\Domain\Reactions\Repositories\Reactions $reactionsRepo)
        {
            $this->reactionsRepo = $reactionsRepo;
        }

        /**
         * addReaction - adds a reaction to an entity, checks if a user has already reacted the same way
         * @access public
         *
         * @param string  $module
         * @param integer $moduleId
         * @param integer $userId
         * @param string  $reaction
         *
         * @return boolean
         */
        public function addReaction(int $userId, string $module, int $moduleId, string $reaction): bool
        {
            if ($module == '' || $moduleId == '' || $userId == '' || $reaction == '') {
                return false;
            }

            //Check if user already reacted in that category
            $userReactions = $this->getUserReactions($userId, $module, $moduleId);

            $currentReactionType = $this->getReactionType($reaction);

            foreach ($userReactions as $previousReaction) {
                if ($this->getReactionType($previousReaction['reaction']) == $currentReactionType) {
                    return false;
                }
            }

            return $this->reactionsRepo->addReaction($userId, $module, $moduleId, $reaction);
        }

        /**
         * getReactionType - returns the category/type of a given reaction
         * @access public
         *
         * @param string $reaction
         *
         * @return string|false
         */
        public function getReactionType($reaction): string|false
        {

            $types = \Leantime\Domain\Reactions\Models\Reactions::getReactions();

            foreach ($types as $reactionType => $reactionValues) {
                if (isset($reactionValues[$reaction])) {
                    return $reactionType;
                }
            }

            return false;
        }

        /**
         * getGroupedEntityReactions - gets all reactions for a given entity grouped and counted by reactions
         * @access public
         *
         * @param string  $module
         * @param integer $moduleId
         *
         * @return array|boolean returns the array on success or false on failure
         */
        public function getGroupedEntityReactions($module, $moduleId): array|false
        {
            return $this->reactionsRepo->getGroupedEntityReactions($module, $moduleId);
        }

        /**
         * getMyReactions - gets user reactions. Can be very broad or very targeted
         * @access public
         *
         * @param integer $userId
         * @param string  $module
         * @param integer $moduleId
         * @param string  $reaction
         *
         * @return array|false
         */
        public function getUserReactions(int $userId, string $module = '', ?int $moduleId = null, string $reaction = ''): array|false
        {

            return $this->reactionsRepo->getUserReactions($userId, $module, $moduleId, $reaction);
        }

        /**
         * addReaction - adds a reaction to an entity, checks if a user has already reacted the same way
         * @access public
         *
         * @param string  $module
         * @param integer $moduleId
         * @param integer $userId
         * @param string  $reaction
         *
         * @return boolean
         */
        public function removeReaction(int $userId, string $module, int $moduleId, string $reaction): bool
        {
            return $this->reactionsRepo->removeUserReaction($userId, $module, $moduleId, $reaction);
        }
    }
}
