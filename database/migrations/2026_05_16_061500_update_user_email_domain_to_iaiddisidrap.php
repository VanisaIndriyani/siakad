<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $oldDomain = '@kampus.ac.id';
        $newDomain = '@iaiddisidrap.ac.id';

        $rows = DB::table('users')
            ->select(['id', 'email'])
            ->where('email', 'like', '%'.$oldDomain)
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            $email = (string) $row->email;
            if (!Str::endsWith($email, $oldDomain)) {
                continue;
            }

            $local = Str::before($email, '@');
            $nextEmail = $local.$newDomain;
            $i = 1;
            while (
                DB::table('users')
                    ->where('email', $nextEmail)
                    ->where('id', '<>', $row->id)
                    ->exists()
            ) {
                $nextEmail = $local."+{$i}".$newDomain;
                $i++;
            }

            DB::table('users')->where('id', $row->id)->update(['email' => $nextEmail]);
        }
    }

    public function down(): void
    {
    }
};

