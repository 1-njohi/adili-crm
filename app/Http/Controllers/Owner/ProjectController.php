<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount(['plots', 'expenses'])->get();

        return Inertia::render('owner/projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        return Inertia::render('owner/projects/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'land_size' => 'nullable|string|max:255',
            'land_size_unit' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:draft,active,sold_out',
            'neighbor_discount' => 'nullable|array',

            // Images
            'primary_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',

            // Microsite fields
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'hero_description' => 'nullable|string',
            'hero_description_2' => 'nullable|string',
            'drone_video_id' => 'nullable|string|max:255',
            'brochure' => 'nullable|file|mimes:pdf|max:10240',
            'starting_price' => 'nullable|numeric|min:0',
            'plot_size' => 'nullable|string|max:255',
            'min_deposit' => 'nullable|numeric|min:0',
            'max_months' => 'nullable|integer|min:1',
            'payment_tiers' => 'nullable|array',
            'feature_groups' => 'nullable|array',
            'amenities' => 'nullable|array',
        ]);

        // Handle primary image
        if ($request->hasFile('primary_image')) {
            $path = $request->file('primary_image')->store('projects', 'public');
            $validated['primary_image'] = $path;
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $file) {
                $galleryPaths[] = $file->store('projects', 'public');
            }
            $validated['gallery_images'] = $galleryPaths;
        }

        // Handle brochure
        if ($request->hasFile('brochure')) {
            $path = $request->file('brochure')->store('brochures', 'public');
            $validated['brochure_path'] = $path;
        }

        // Encode JSON fields if they are strings
        $jsonFields = ['payment_tiers', 'feature_groups', 'amenities'];
        foreach ($jsonFields as $field) {
            if (isset($validated[$field]) && is_string($validated[$field])) {
                $validated[$field] = json_decode($validated[$field], true);
            }
        }

        $project = Project::create($validated);

        return redirect()->route('owner.projects.index')
            ->with('success', 'Project created successfully!');
    }

    public function show(Project $project)
    {
        $project->load(['plots', 'expenses', 'agents']);

        return Inertia::render('owner/projects/Show', [
            'project' => $project,
        ]);
    }

    public function edit(Project $project)
    {
        return Inertia::render('owner/projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'land_size' => 'nullable|string|max:255',
            'land_size_unit' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:draft,active,sold_out',
            'neighbor_discount' => 'nullable|array',

            // Images
            'primary_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'remove_primary' => 'nullable|boolean',
            'remove_gallery' => 'nullable|array',

            // Microsite fields
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'hero_description' => 'nullable|string',
            'hero_description_2' => 'nullable|string',
            'drone_video_id' => 'nullable|string|max:255',
            'brochure' => 'nullable|file|mimes:pdf|max:10240',
            'starting_price' => 'nullable|numeric|min:0',
            'plot_size' => 'nullable|string|max:255',
            'min_deposit' => 'nullable|numeric|min:0',
            'max_months' => 'nullable|integer|min:1',
            'payment_tiers' => 'nullable|array',
            'feature_groups' => 'nullable|array',
            'amenities' => 'nullable|array',
        ]);

        // Handle primary image removal
        if ($request->boolean('remove_primary') && $project->primary_image) {
            Storage::disk('public')->delete($project->primary_image);
            $validated['primary_image'] = null;
        }

        // Handle new primary image
        if ($request->hasFile('primary_image')) {
            if ($project->primary_image) {
                Storage::disk('public')->delete($project->primary_image);
            }
            $path = $request->file('primary_image')->store('projects', 'public');
            $validated['primary_image'] = $path;
        }

        // Handle gallery removal
        $removedGalleries = $request->input('remove_gallery', []);
        if (! empty($removedGalleries) && $project->gallery_images) {
            $remaining = array_diff($project->gallery_images, $removedGalleries);
            $validated['gallery_images'] = array_values($remaining);
            foreach ($removedGalleries as $removed) {
                Storage::disk('public')->delete($removed);
            }
        }

        // Handle new gallery images
        if ($request->hasFile('gallery_images')) {
            $existing = $project->gallery_images ?? [];
            foreach ($request->file('gallery_images') as $file) {
                $existing[] = $file->store('projects', 'public');
            }
            $validated['gallery_images'] = $existing;
        }

        // Handle brochure
        if ($request->hasFile('brochure')) {
            if ($project->brochure_path) {
                Storage::disk('public')->delete($project->brochure_path);
            }
            $path = $request->file('brochure')->store('brochures', 'public');
            $validated['brochure_path'] = $path;
        }

        // Encode JSON fields if they are strings
        $jsonFields = ['payment_tiers', 'feature_groups', 'amenities'];
        foreach ($jsonFields as $field) {
            if (isset($validated[$field]) && is_string($validated[$field])) {
                $validated[$field] = json_decode($validated[$field], true);
            }
        }

        $project->update($validated);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        // Delete associated images
        if ($project->primary_image) {
            Storage::disk('public')->delete($project->primary_image);
        }
        if ($project->gallery_images) {
            foreach ($project->gallery_images as $file) {
                Storage::disk('public')->delete($file);
            }
        }
        if ($project->brochure_path) {
            Storage::disk('public')->delete($project->brochure_path);
        }

        $project->delete();

        return redirect()->route('owner.projects.index')
            ->with('success', 'Project deleted successfully!');
    }
}
