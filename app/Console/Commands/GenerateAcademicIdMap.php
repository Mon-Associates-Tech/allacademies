<?php

namespace App\Console\Commands;

use App\Models\AcademicGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAcademicIdMap extends Command
{
    protected $signature = 'academic:id-map {--output=academic_id_map.json}';

    protected $description = 'Generate a hierarchical ID map of all academic entities';

    public function handle()
    {
        $this->info('Generating Academic ID Map...');

        $map = [];

        $groups = AcademicGroup::with([
            'academicLevels.subjects.topics.subtopics'
        ])->get();

        foreach ($groups as $group) {
            $groupData = [
                'id' => $group->id,
                'name' => $group->name,
                'tag' => $group->tag,
                'levels' => [],
            ];

            foreach ($group->academicLevels as $level) {
                $levelData = [
                    'id' => $level->id,
                    'name' => $level->name,
                    'label' => $level->label,
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'subjects' => [],
                ];

                foreach ($level->subjects as $subject) {
                    $subjectData = [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'code' => $subject->code,
                        'level_id' => $level->id,
                        'level_name' => $level->name,
                        'group_id' => $group->id,
                        'group_name' => $group->name,
                        'topics' => [],
                    ];

                    foreach ($subject->topics as $topic) {
                        // Count questions for this topic
                        $essayCount = DB::table('essay_questions')
                            ->where('academic_topic_id', $topic->id)
                            ->count();
                        $mcqCount = DB::table('multiple_choice_questions')
                            ->where('academic_topic_id', $topic->id)
                            ->count();
                        $tofCount = DB::table('true_or_false_questions')
                            ->where('academic_topic_id', $topic->id)
                            ->count();

                        $topicData = [
                            'id' => $topic->id,
                            'name' => $topic->name,
                            'subject_id' => $subject->id,
                            'subject_name' => $subject->name,
                            'level_id' => $level->id,
                            'level_name' => $level->name,
                            'group_id' => $group->id,
                            'group_name' => $group->name,
                            'questions' => [
                                'essay' => $essayCount,
                                'multiple_choice' => $mcqCount,
                                'true_or_false' => $tofCount,
                                'total' => $essayCount + $mcqCount + $tofCount,
                            ],
                            'subtopics' => [],
                        ];

                        foreach ($topic->subtopics as $subtopic) {
                            // Count questions for this subtopic
                            $subEssayCount = DB::table('essay_questions')
                                ->where('academic_subtopic_id', $subtopic->id)
                                ->count();
                            $subMcqCount = DB::table('multiple_choice_questions')
                                ->where('academic_subtopic_id', $subtopic->id)
                                ->count();
                            $subTofCount = DB::table('true_or_false_questions')
                                ->where('academic_subtopic_id', $subtopic->id)
                                ->count();

                            $topicData['subtopics'][] = [
                                'id' => $subtopic->id,
                                'name' => $subtopic->name,
                                'topic_id' => $topic->id,
                                'topic_name' => $topic->name,
                                'subject_id' => $subject->id,
                                'subject_name' => $subject->name,
                                'level_id' => $level->id,
                                'level_name' => $level->name,
                                'group_id' => $group->id,
                                'group_name' => $group->name,
                                'questions' => [
                                    'essay' => $subEssayCount,
                                    'multiple_choice' => $subMcqCount,
                                    'true_or_false' => $subTofCount,
                                    'total' => $subEssayCount + $subMcqCount + $subTofCount,
                                ],
                            ];
                        }

                        $subjectData['topics'][] = $topicData;
                    }

                    $levelData['subjects'][] = $subjectData;
                }

                $groupData['levels'][] = $levelData;
            }

            $map[] = $groupData;
        }

        $outputFile = $this->option('output');
        $fullPath = storage_path('app/' . $outputFile);

        file_put_contents($fullPath, json_encode($map, JSON_PRETTY_PRINT));

        $this->info("Academic ID Map generated successfully!");
        $this->info("File saved to: {$fullPath}");

        // Display summary
        $totalGroups = count($map);
        $totalLevels = collect($map)->sum(fn($g) => count($g['levels']));
        $totalSubjects = collect($map)->sum(fn($g) => 
            collect($g['levels'])->sum(fn($l) => count($l['subjects']))
        );
        $totalTopics = collect($map)->sum(fn($g) => 
            collect($g['levels'])->sum(fn($l) => 
                collect($l['subjects'])->sum(fn($s) => count($s['topics']))
            )
        );
        $totalSubtopics = collect($map)->sum(fn($g) => 
            collect($g['levels'])->sum(fn($l) => 
                collect($l['subjects'])->sum(fn($s) => 
                    collect($s['topics'])->sum(fn($t) => count($t['subtopics']))
                )
            )
        );

        $this->table(
            ['Entity', 'Count'],
            [
                ['Academic Groups', $totalGroups],
                ['Academic Levels', $totalLevels],
                ['Academic Subjects', $totalSubjects],
                ['Academic Topics', $totalTopics],
                ['Academic Subtopics', $totalSubtopics],
            ]
        );

        return Command::SUCCESS;
    }
}
