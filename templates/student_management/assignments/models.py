from django.db import models
class Assignment(models.Model):
    name = models.CharField(max_length=100)
    user_type = models.CharField(max_length=10, choices=[('student', 'Student'), ('faculty', 'faculty')])
    title = models.CharField(max_length=200)
    file = models.FileField(upload_to='uploads/')
    submitted_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.title} by {self.name}"
# Create your models here.
