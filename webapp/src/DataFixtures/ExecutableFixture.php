<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Executable;
use App\Entity\ExecutableFile;
use App\Entity\ImmutableExecutable;
use App\Service\DOMJudgeService;
use Doctrine\Persistence\ObjectManager;
use ZipArchive;

/**
 * Class ExecutableFixture
 * @package App\DataFixtures
 */
class ExecutableFixture extends AbstractExampleDataFixture
{
    const BOOLFIND_CMP_REFERENCE = 'boolfind-cmp';
    const BOOLFIND_RUN_REFERENCE = 'boolfind-run';

    /**
     * @var string
     */
    protected $sqlDir;

    /**
     * @var DOMJudgeService
     */
    protected $dj;

    public function __construct(string $sqlDir, DOMJudgeService $dj)
    {
        $this->sqlDir = $sqlDir;
        $this->dj = $dj;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $boolfindCompareFile = sprintf(
            '%s/files/examples/boolfind_cmp.zip',
            $this->sqlDir
        );
        $boolfindCompare     = new Executable();
        $boolfindCompare
            ->setExecid('boolfind_cmp')
            ->setDescription('boolfind comparator')
            ->setType('compare')
            ->setImmutableExecutable($this->createImmutableExecutable($boolfindCompareFile));

        $boolfindRunFile = sprintf(
            '%s/files/examples/boolfind_run.zip',
            $this->sqlDir
        );
        $boolfindRun     = new Executable();
        $boolfindRun
            ->setExecid('boolfind_run')
            ->setDescription('boolfind run script')
            ->setType('run')
            ->setImmutableExecutable($this->createImmutableExecutable($boolfindRunFile));

        $manager->persist($boolfindCompare);
        $manager->persist($boolfindRun);
        $manager->flush();

        $this->addReference(self::BOOLFIND_CMP_REFERENCE, $boolfindCompare);
        $this->addReference(self::BOOLFIND_RUN_REFERENCE, $boolfindRun);
    }

    private function createImmutableExecutable(string $filename): ImmutableExecutable
    {
        $zip = new ZipArchive();
        $zip->open($filename, ZIPARCHIVE::CHECKCONS);
        return $this->dj->createImmutableExecutable($zip);
    }
}
