<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class NewTablesMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $users = $this->table("users");
        $users->addColumn("name", "string", ["limit" => 50])
            ->addColumn("username", "string", ["limit" => 50])
            ->addColumn("email", "string", ["limit" => 50])
            ->addColumn("password", "string", ["limit" => 50])
            ->addColumn("profile_pic", "string", ["default" => "/images/account/default.jpg", "null" => true])
            ->addColumn("active", "boolean", ["default" => true, "null" => true])
            ->create();

        $societies = $this->table("societies");
        $societies->addColumn("banner", "string", ["null" => true, "default" => "/images/society/banner.jpg"])
            ->addColumn("name", "string", ["limit" => 50])
            ->addColumn("description", "string", ["limit" => 500, "null" => true])
            ->create();

        $events = $this->table("events");
        $events->addColumn("name", "string", ["limit" => 50])
            ->addColumn("description", "string", ["limit" => 500])
            ->addColumn("created_on", "datetime")
            ->addColumn("location", "string", ["limit" => 200])
            ->addColumn("lat", "float")
            ->addColumn("lon", "float")
            ->addColumn("date_and_time", "datetime")
            ->addColumn("passed", "boolean", ["default" => false, "null" => true])
            ->addColumn("created_by", "integer", ["signed"=>false])
            ->addColumn("society_id", "integer", ["signed"=>false])
            ->addForeignKey("created_by", "users", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->addForeignKey("society_id", "societies", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->create();

        $comments = $this->table("comments");
        $comments->addColumn("body", "string", ["limit" => 300])
            ->addColumn("user_id", "integer", ["null" => true, "signed"=>false])
            ->addColumn("event_id", "integer", ["signed"=>false])
            ->addForeignKey("user_id", "users", "id", ["delete" => "SET_NULL", "update" => "CASCADE"])
            ->addForeignKey("event_id", "events", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->create();

        $links = $this->table("links");
        $links->addColumn("uri", "string", ["limit" => 100])
            ->addColumn("date_created", "date")
            ->addColumn("date_expires", "date")
            ->addColumn("society_id", "integer", ["signed"=>false])
            ->addForeignKey("society_id", "societies", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->create();

        $attendees = $this->table("attendees", ["id" => false, "primary_key" => ["event_id", "user_id"]]);
        $attendees->addColumn("event_id", "integer", ["signed"=>false])
            ->addColumn("user_id", "integer", ["signed"=>false])
            ->addForeignKey("event_id", "events", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->addForeignKey("user_id", "users", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->create();

        $members = $this->table("members", ["id" => false, "primary_key" => ["user_id", "society_id"]]);
        $members->addColumn("user_id", "integer", ["signed"=>false])
            ->addColumn("society_id", "integer", ["signed"=>false])
            ->addForeignKey("society_id", "societies", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->addForeignKey("user_id", "users", "id", ["delete" => "CASCADE", "update" => "CASCADE"])
            ->create();
    }
}
