CREATE TABLE thg__challenge_view
(
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    notebook_id  VARCHAR(6)   NOT NULL,
    challenge_id VARCHAR(4)   NOT NULL,

    CONSTRAINT `thg__challenge_view_pk` PRIMARY KEY (id),
    CONSTRAINT `thg__challenge_view_to_notebook_fk` FOREIGN KEY
        (notebook_id) REFERENCES th__notebook (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `thg__challenge_view_to_challenge_fk` FOREIGN KEY
        (challenge_id) REFERENCES th__challenge (id)
        ON DELETE CASCADE ON UPDATE CASCADE
)
