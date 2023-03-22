<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2022 Varun Patil <radialapps@gmail.com>
 * @author Varun Patil <radialapps@gmail.com>
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Memories\Controller;

class PeopleRecognizeController extends GenericClusterController
{
    use PeopleControllerUtils;

    protected function appName(): string
    {
        return 'Recognize';
    }

    protected function isEnabled(): bool
    {
        return $this->recognizeIsEnabled();
    }

    protected function getClusters(): array
    {
        return $this->timelineQuery->getPeopleRecognize($this->root, $this->getUID());
    }

    protected function getFiles(string $name, ?int $limit = null): array
    {
        return $this->timelineQuery->getPeopleRecognizeFiles((int) $name, $this->root, $limit) ?? [];
    }

    protected function sortFilesForPreview(array &$files)
    {
        $this->sortByScores($files);
    }

    protected function getPreviewBlob($file, $object): array
    {
        return $this->cropFace($file, $object, 1.5);
    }
}