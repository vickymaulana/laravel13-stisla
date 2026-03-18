# Learning Guide - Laravel 13 Stisla Template

Welcome! This guide will help you learn Laravel using this template.

## 🎯 Learning Objectives

After working through this template, you will understand:
- Laravel MVC architecture
- Authentication and authorization
- Database operations with Eloquent ORM
- Form handling and validation
- Routing and middleware
- Email notifications
- Frontend integration with Vite

## 📖 Step-by-Step Learning Path

### Week 1: Laravel Basics

#### Day 1-2: Understanding the Structure
- [ ] Read through the [README.MD](README.MD)
- [ ] Explore the project folder structure
- [ ] Understand the purpose of each folder
- [ ] Set up the development environment

**Resources:**
- [Laravel Documentation - Directory Structure](https://laravel.com/docs/structure)

#### Day 3-4: Routing
- [ ] Open `routes/web.php`
- [ ] Study how routes are defined
- [ ] Learn about route groups and middleware
- [ ] Try creating a new route

**Exercise:** Create a route for `/about` page

**Resources:**
- [Laravel Documentation - Routing](https://laravel.com/docs/routing)

#### Day 5-7: Controllers
- [ ] Study `ProfileController.php`
- [ ] Understand controller methods
- [ ] Learn about dependency injection
- [ ] Create your own controller

**Exercise:** Create a `PageController` with an about() method

```bash
php artisan make:controller PageController
```

**Resources:**
- [Laravel Documentation - Controllers](https://laravel.com/docs/controllers)

### Week 2: Database and Models

#### Day 1-3: Migrations
- [ ] Review migrations in `database/migrations/`
- [ ] Understand migration structure
- [ ] Create a new migration
- [ ] Run migrations

**Exercise:** Create a migration for a "posts" table

```bash
php artisan make:migration create_posts_table
php artisan migrate
```

**Resources:**
- [Laravel Documentation - Migrations](https://laravel.com/docs/migrations)

#### Day 4-5: Eloquent Models
- [ ] Study the `User.php` model
- [ ] Learn about mass assignment
- [ ] Understand model relationships
- [ ] Create a new model

**Exercise:** Create a Post model

```bash
php artisan make:model Post -m
```

**Resources:**
- [Laravel Documentation - Eloquent](https://laravel.com/docs/eloquent)

#### Day 6-7: Database Queries
- [ ] Practice CRUD operations
- [ ] Learn query builder methods
- [ ] Understand eager loading
- [ ] Try complex queries

**Exercise:** Retrieve all posts with their authors

```php
$posts = Post::with('user')->get();
```

### Week 3: Views and Authentication

#### Day 1-3: Blade Templates
- [ ] Explore views in `resources/views/`
- [ ] Learn Blade syntax
- [ ] Understand template inheritance
- [ ] Create a custom view

**Exercise:** Create a posts listing page

**Resources:**
- [Laravel Documentation - Blade](https://laravel.com/docs/blade)

#### Day 4-7: Authentication
- [ ] Study the authentication system
- [ ] Learn about middleware
- [ ] Understand password hashing
- [ ] Implement custom auth logic

**Exercise:** Add "Remember Me" functionality

**Resources:**
- [Laravel Documentation - Authentication](https://laravel.com/docs/authentication)

### Week 4: Advanced Features

#### Day 1-2: Form Validation
- [ ] Study validation in `ProfileController.php`
- [ ] Learn validation rules
- [ ] Create custom validation
- [ ] Handle validation errors

**Exercise:** Add validation for a contact form

**Resources:**
- [Laravel Documentation - Validation](https://laravel.com/docs/validation)

#### Day 3-4: Email Notifications
- [ ] Study `OtpPasswordResetNotification.php`
- [ ] Learn about notifications
- [ ] Configure mail settings
- [ ] Send test emails

**Exercise:** Create a welcome email notification

```bash
php artisan make:notification WelcomeNotification
```

**Resources:**
- [Laravel Documentation - Notifications](https://laravel.com/docs/notifications)

#### Day 5-7: Build Your Feature
- [ ] Plan a new feature (e.g., blog posts)
- [ ] Create necessary models and migrations
- [ ] Build controllers and views
- [ ] Test your feature

## 🛠️ Practical Exercises

### Exercise 1: Create a Blog System

**Goal:** Add a simple blog to the template

**Steps:**
1. Create Post model and migration
2. Add posts table (title, content, user_id, timestamps)
3. Create PostController with CRUD methods
4. Build views for listing and showing posts
5. Add routes for posts
6. Implement authorization (only author can edit)

### Exercise 2: Add Categories

**Goal:** Categorize blog posts

**Steps:**
1. Create Category model
2. Set up many-to-many relationship
3. Add category selection in post form
4. Display categories on post pages
5. Create category filter

### Exercise 3: User Roles Enhancement

**Goal:** Improve the role system

**Steps:**
1. Study the existing `hakakses` model
2. Add more roles (admin, editor, viewer)
3. Create middleware for each role
4. Implement role-based permissions
5. Add role management interface

## 📚 Additional Resources

### Official Documentation
- [Laravel 13 Documentation](https://laravel.com/docs)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3)
- [Stisla Documentation](https://getstisla.com/docs)

### Video Tutorials
- [Laravel From Scratch (Laracasts)](https://laracasts.com/series/laravel-from-scratch)
- [Laravel Daily YouTube Channel](https://www.youtube.com/c/LaravelDaily)

### Community
- [Laravel Discord](https://discord.gg/laravel)
- [Laravel Forums](https://laracasts.com/discuss)
- [Stack Overflow - Laravel Tag](https://stackoverflow.com/questions/tagged/laravel)

## 💡 Tips for Learning

1. **Code Along**: Don't just read - type the code yourself
2. **Break Things**: Experiment and see what happens
3. **Read Error Messages**: They often tell you exactly what's wrong
4. **Use Laravel Tinker**: Test code in the REPL
   ```bash
   php artisan tinker
   ```
5. **Check Logs**: Look in `storage/logs/` when things go wrong
6. **Ask Questions**: Use GitHub issues or Laravel communities
7. **Build Projects**: Apply what you learn in real projects

## 🎓 Quiz Yourself

After each week, test your knowledge:

**Week 1:**
- What is the purpose of routes in Laravel?
- How do you pass data from a controller to a view?
- What is middleware used for?

**Week 2:**
- What is Eloquent ORM?
- How do you create a new database record?
- What's the difference between `get()` and `first()`?

**Week 3:**
- What is Blade templating?
- How does Laravel hash passwords?
- What middleware protects authenticated routes?

**Week 4:**
- How do you validate form input?
- What are Laravel notifications?
- How do you send an email in Laravel?

## 🏆 Next Steps

Once you're comfortable with this template:

1. **Build Your Own Project**
   - Apply what you've learned
   - Start small and iterate

2. **Learn Testing**
   - Write PHPUnit tests
   - Learn about feature and unit tests

3. **Explore Advanced Topics**
   - Queues and jobs
   - Events and listeners
   - API development with Laravel Sanctum
   - Real-time features with Laravel Echo

4. **Contribute Back**
   - Improve this template
   - Help others in the community
   - Share your learning journey

Good luck with your Laravel learning journey! 🚀
