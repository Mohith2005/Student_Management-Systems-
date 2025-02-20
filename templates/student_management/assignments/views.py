from django.shortcuts import render
from .forms import AssignmentForm

def upload_assignment(request):
    if request.method == 'POST':
        form = AssignmentForm(request.POST, request.FILES)
        if form.is_valid():
            form.save()
            return render(request, 'upload_success.html')
    else:
        form = AssignmentForm()
    return render(request, 'upload.html', {'form': form})
