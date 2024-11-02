<?php
// database/seeders/CoeClassesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CoeClass;

class CoeClassesSeeder extends Seeder
{
    public function run()
    {
        $coeClasses = [
            [
                'name' => 'Artificial Intelligence and Robotics',
                'description' => 'Focuses on the development of AI algorithms, robotics technology, and automation solutions for various industries.'
            ],
            [
                'name' => 'Biotechnology and Bioprocess',
                'description' => 'Specializes in biotechnological advancements and bioprocessing techniques for pharmaceuticals, agriculture, and environmental applications.'
            ],
            [
                'name' => 'Construction Quality and Technology',
                'description' => 'Dedicated to research and innovation in construction materials, quality control, and advanced construction technologies.'
            ],
            [
                'name' => 'High Performance Computing And Big Data Analytics',
                'description' => 'Specializes in advanced computing technologies, data processing algorithms, and data-driven decision-making tools.'
            ],
            [
                'name' => 'Mineral Exploration Extraction and Processing',
                'description' => 'Advances sustainable mining practices, mineral resource management, and innovative processing techniques.'
            ],
            [
                'name' => 'Nano Technology',
                'description' => 'Focuses on nanotechnology research, exploring manipulation of matter at the atomic and molecular scale for various applications.'
            ],
            [
                'name' => 'Nuclear Reactor Technology',
                'description' => 'Dedicated to nuclear science and engineering, focusing on reactor design, nuclear safety, and sustainable use of nuclear energy.'
            ],
            [
                'name' => 'Sustainable Energy',
                'description' => 'Committed to researching and developing sustainable energy technologies, focusing on renewable energy sources and energy efficiency.'
            ],
        ];

        foreach ($coeClasses as $class) {
            CoeClass::create($class);
        }
    }
}
