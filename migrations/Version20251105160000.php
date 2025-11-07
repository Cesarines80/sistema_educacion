<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251105160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update schema for ManyToMany relationship between Subject and AcademicGrade';
    }

    public function up(Schema $schema): void
    {
        // Create the join table for ManyToMany relationship
        $this->addSql('CREATE TABLE academic_grade_subject (academic_grade_id INT NOT NULL, subject_id INT NOT NULL, INDEX IDX_1234567890 (academic_grade_id), INDEX IDX_0987654321 (subject_id), PRIMARY KEY(academic_grade_id, subject_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE academic_grade_subject ADD CONSTRAINT FK_1234567890 FOREIGN KEY (academic_grade_id) REFERENCES academic_grade (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE academic_grade_subject ADD CONSTRAINT FK_0987654321 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');

        // Update existing data if needed (migrate from ManyToOne to ManyToMany)
        // This assumes that subjects had a single academic_grade before
        $this->addSql('INSERT INTO academic_grade_subject (academic_grade_id, subject_id) SELECT academic_grade_id, id FROM subject WHERE academic_grade_id IS NOT NULL');

        // Remove the old column
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_subject_academic_grade');
        $this->addSql('DROP INDEX IDX_subject_academic_grade ON subject');
        $this->addSql('ALTER TABLE subject DROP COLUMN academic_grade_id');

        // Update other fields
        $this->addSql('ALTER TABLE student CHANGE phone phone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE teacher CHANGE phone phone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Reverse the changes
        $this->addSql('ALTER TABLE subject ADD academic_grade_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_subject_academic_grade FOREIGN KEY (academic_grade_id) REFERENCES academic_grade (id)');
        $this->addSql('CREATE INDEX IDX_subject_academic_grade ON subject (academic_grade_id)');

        // Migrate data back (this will lose multiple relationships)
        $this->addSql('UPDATE subject SET academic_grade_id = (SELECT academic_grade_id FROM academic_grade_subject WHERE subject_id = subject.id LIMIT 1)');

        $this->addSql('DROP TABLE academic_grade_subject');

        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE teacher CHANGE phone phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE student CHANGE phone phone VARCHAR(255) DEFAULT NULL');
    }
}
