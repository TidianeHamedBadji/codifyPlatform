<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Chambre;
use App\Models\Etudiant;
use App\Models\Pavillon;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;
use App\Http\Resources\ChambreRessource;

/**
 * @OA\Tag(
 *     name="Chambres",
 *     description="Endpoints pour la gestion des chambres."
 * )
 */
class ChambreController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/chambres",
     *     summary="Récupérer la liste des chambres.",
     *     @OA\Response(response="200", description="Liste des chambres."),
     * )
     */
    public function index()
    {
            //  $chambres = Chambre::all();
            $chambres = Chambre::with('pavillons')->get();
            return response()->json(ChambreRessource::collection($chambres));
    }
      /**
     * @OA\Post(
     *     path="/api/chambre/create",
     *     summary="Créer une nouvelle chambre.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"libelle", "type_chambre", "nombres_lits", "nombres_limites", "pavillons_id", "etudiants_id"},
     *             @OA\Property(property="libelle", type="string"),
     *             @OA\Property(property="type_chambre", type="string"),
     *             @OA\Property(property="nombres_lits", type="integer"),
     *             @OA\Property(property="nombres_limites", type="integer"),
     *             @OA\Property(property="pavillons_id", type="integer"),
     *             @OA\Property(property="etudiants_id", type="integer"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Chambre créée avec succès."),
     *     @OA\Response(response="404", description="Erreur lors de la création de la chambre."),
     * )
     */

     public function store(Request $request, Pavillon $pavillon)
    {
        $request->validate([
            'libelle' => ['required'],
            'type_chambre' => ['required'],
            'nombres_lits' => ['required'],
            'nombres_limites' => ['required', 'numeric'],
        ]);

        if (!$pavillon) {
            return response()->json([
                'message' => 'Le pavillon d\'ID saisi n\'existe pas.',
            ], 404);
        }else {
            $chambre = new Chambre();
            $chambre->libelle = $request->input('libelle');
            $chambre->type_chambre = $request->input('type_chambre');
            $chambre->nombres_lits = $request->input('nombres_lits');
            $chambre->nombres_limites = $request->input('nombres_limites');
            $chambre->pavillons_id = $pavillon->id;
            if ($chambre->nombres_limites > 12 && $chambre->nombres_lits = 3 ) {
                return response()->json([
                    'Message' => 'La chambre est pleine, son nombre maximal est 12',
                ], 200);
            }
            if ($chambre->save()) {
                return response()->json([
                    'Message' => 'Success!',
                    'Chambre créé' => $chambre
                ], 200);
            } else {
                return response([
                    'Message' => 'Création réclamation impossible.',
                ], 500);
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/api/chambre/read/{id}",
     *     summary="Récupérer les détails d'une chambre spécifique.",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la chambre", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Chambre trouvée."),
     *     @OA\Response(response="500", description="Erreur lors de la recherche de la chambre."),
     * )
     */
    public function show(string $id){

        $chambre = Chambre::find($id);

        if ($chambre){

            return response()->json([
                'Message' => 'Chambre trouvé.',
                'Chambre' => $chambre
            ], 200);

        } else {

            return response([

                'Message' => 'La chambre n\'est pas trouvé.',

            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/chambre/update/{id}",
     *     summary="Mettre à jour les détails d'une chambre spécifique.",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la chambre", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"libelle", "type_chambre", "nombres_lits", "nombres_limites", "pavillons_id", "etudiants_id"},
     *             @OA\Property(property="libelle", type="string"),
     *             @OA\Property(property="type_chambre", type="string"),
     *             @OA\Property(property="nombres_lits", type="integer"),
     *             @OA\Property(property="nombres_limites", type="integer"),
     *             @OA\Property(property="pavillons_id", type="integer"),
     *             @OA\Property(property="etudiants_id", type="integer"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Chambre mise à jour avec succès."),
     *     @OA\Response(response="500", description="Erreur lors de la mise à jour de la chambre."),
     * )
     */
    // public function update(Request $request, $id)
    // {
    //     $chambre = Chambre::find($id);

    //     if (!$chambre) {
    //         return response()->json([
    //             'message' => 'La chambre d\'ID saisi n\'existe pas.',
    //         ], 404);
    //     }else{
    //         $request->validate([
    //             'libelle' => ['required'],
    //             'type_chambre' => ['required'],
    //             'nombres_lits' => ['required'],
    //             'nombres_limites' => ['required', 'numeric', 'max:12'],
    //         ]);
    //         $chambre->libelle = $request->input('libelle');
    //         $chambre->type_chambre = $request->input('type_chambre');
    //         $chambre->nombres_lits = $request->input('nombres_lits');
    //         $chambre->nombres_limites = $request->input('nombres_limites');
    //         $chambre->pavillons_id = $pavillon->id;
    //         if ($chambre->save()) {
    //             return response()->json([
    //                 'Message' => 'Mise à jour de la chambre réussie!',
    //                 'Chambre' => $chambre
    //             ], 200);
    //         } else {
    //             return response([
    //                 'Message' => 'Mise à jour de la chambre impossible.',
    //             ], 500);
    //         }
    //     }
    // }
    public function update(Request $request, $id, Chambre $chambre)
    {
        try {
            $chambre= Chambre::find($id);
            if (!$chambre) {
                return response()->json([
                    "status_code" => 404,
                    "status_messages" => "La chambre d\'ID saisi n\'existe pas."
                ]);
            } else {
                $request->validate([
                'libelle' => ['required'],
                'type_chambre' => ['required'],
                'nombres_lits' => ['required'],
                'nombres_limites' => ['required', 'numeric', 'max:12'],
                ]);
               // dd($request);
                $chambre->libelle = $request->input('libelle');
                $chambre->type_chambre = $request->input('type_chambre');
                $chambre->nombres_lits = $request->input('nombres_lits');
                $chambre->update();
                return response()->json([
                    "status_code" => 200,
                    "status_message" => "Chambre modifié avec succés",
                    "chambre    " => $chambre
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

   /**
     * @OA\Delete(
     *     path="/api/chambre/delete/{id}",
     *     summary="Supprimer une chambre spécifique.",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID de la chambre", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Chambre supprimée avec succès."),
     *     @OA\Response(response="500", description="Erreur lors de la suppression de la chambre."),
     * )
     */
    public function destroy(string $id){

        $chambre = Chambre::find($id);

        if($chambre){

            $chambre->delete();

            return response()->json([

                'Message' => 'chambre supprimée avec success.',

            ], 200);

        }else {

            return response([

                'Message' => 'Nous n\'avons pas trouvé la chambre.',

            ], 500);
        }

    }
}
