create table comments
(
    id      bigint unsigned auto_increment,
    user_id int  null,
    body    text null,
    constraint id
        unique (id)
);

