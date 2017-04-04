<?php
/* Template Name: Base Page Template */
?>

@extends('templates.base')

@section('content')
    <?php while (have_posts()): the_post() ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
@endsection