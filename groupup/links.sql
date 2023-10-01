create table links
(
    id         bigint unsigned auto_increment,
    society    int          null,
    body       varchar(255) null,
    created_on datetime     null,
    constraint id
        unique (id)
);

