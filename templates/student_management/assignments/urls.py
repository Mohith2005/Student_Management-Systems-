from django.urls import path
from .views import upload_assignment, success

urlpatterns = [
    path('upload/', upload_assignment, name='upload'),
    path('success/', success, name='success'),
]

