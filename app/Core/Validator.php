<?php

namespace App\Core;

/**
 * Classe Validator
 * Valide les données de formulaires en utilisant les règles de ValidationRules
 */
class Validator
{
    /**
     * Données à valider
     * @var array
     */
    private array $data = [];

    /**
     * Règles de validation
     * @var array
     */
    private array $rules = [];

    /**
     * Messages d'erreur personnalisés
     * @var array
     */
    private array $customMessages = [];

    /**
     * Erreurs de validation
     * @var array
     */
    private array $errors = [];

    /**
     * Messages d'erreur par défaut en français
     * @var array
     */
    private array $defaultMessages = [
        'required' => 'Le champ :field est obligatoire.',
        'email' => 'Le champ :field doit être une adresse email valide.',
        'min' => 'Le champ :field doit contenir au moins :param caractères.',
        'max' => 'Le champ :field ne peut pas dépasser :param caractères.',
        'between' => 'Le champ :field doit contenir entre :param caractères.',
        'numeric' => 'Le champ :field doit être numérique.',
        'integer' => 'Le champ :field doit être un nombre entier.',
        'url' => 'Le champ :field doit être une URL valide.',
        'alpha' => 'Le champ :field ne peut contenir que des lettres.',
        'alphanumeric' => 'Le champ :field ne peut contenir que des lettres et chiffres.',
        'match' => 'Le champ :field ne correspond pas.',
        'in' => 'Le champ :field contient une valeur invalide.',
        'notIn' => 'Le champ :field contient une valeur non autorisée.',
        'unique' => 'Ce :field existe déjà.',
        'exists' => 'Ce :field n\'existe pas.',
        'date' => 'Le champ :field doit être une date valide.',
        'after' => 'Le champ :field doit être après :param.',
        'before' => 'Le champ :field doit être avant :param.',
        'image' => 'Le fichier :field doit être une image valide (jpg, png, gif, webp).',
        'fileSize' => 'Le fichier :field est trop volumineux (max :param Mo).',
        'strongPassword' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.',
        'phoneNumber' => 'Le numéro de téléphone doit être au format malgache (ex: 03X XX XXX XX).',
        'validName' => 'Le nom contient des caractères invalides ou trop de voyelles successives.',
        'regex' => 'Le format du champ :field est invalide.',
    ];

    /**
     * Labels personnalisés pour les champs
     * @var array
     */
    private array $fieldLabels = [];

    /**
     * Constructeur
     * 
     * @param array $data Données à valider
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Définir les données à valider
     * 
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Définir les règles de validation
     * 
     * @param array $rules ['field' => 'rule1|rule2|rule3:param']
     * @return self
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Définir des messages personnalisés
     * 
     * @param array $messages ['field.rule' => 'Message personnalisé']
     * @return self
     */
    public function setMessages(array $messages): self
    {
        $this->customMessages = $messages;
        return $this;
    }

    /**
     * Définir des labels pour les champs
     * 
     * @param array $labels ['field' => 'Label affiché']
     * @return self
     */
    public function setLabels(array $labels): self
    {
        $this->fieldLabels = $labels;
        return $this;
    }

    /**
     * Valider les données
     * 
     * @return bool
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $rulesString) {
            $rules = explode('|', $rulesString);

            foreach ($rules as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Appliquer une règle de validation
     * 
     * @param string $field Nom du champ
     * @param string $rule Règle à appliquer
     * @return void
     */
    private function applyRule(string $field, string $rule): void
    {
        // Séparer la règle de ses paramètres (ex: min:8)
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

        // Récupérer la valeur du champ
        $value = $this->data[$field] ?? null;

        // Appeler la méthode de validation correspondante
        $isValid = $this->callValidationRule($ruleName, $value, $params, $field);

        // Si invalide, ajouter l'erreur
        if (!$isValid) {
            $this->addError($field, $ruleName, $params);
        }
    }

    /**
     * Appeler la règle de validation appropriée
     * 
     * @param string $ruleName
     * @param mixed $value
     * @param array $params
     * @param string $field
     * @return bool
     */
    private function callValidationRule(string $ruleName, $value, array $params, string $field): bool
    {
        // Gestion spéciale pour 'required'
        if ($ruleName === 'required') {
            return ValidationRules::required($value);
        }

        // Si le champ est vide et pas 'required', on skip la validation
        if (!ValidationRules::required($value)) {
            return true;
        }

        // Appeler la règle correspondante dans ValidationRules
        switch ($ruleName) {
            case 'email':
                return ValidationRules::email($value);

            case 'min':
                return ValidationRules::min($value, (int)$params[0]);

            case 'max':
                return ValidationRules::max($value, (int)$params[0]);

            case 'between':
                return ValidationRules::between($value, (int)$params[0], (int)$params[1]);

            case 'numeric':
                return ValidationRules::numeric($value);

            case 'integer':
                return ValidationRules::integer($value);

            case 'url':
                return ValidationRules::url($value);

            case 'alpha':
                return ValidationRules::alpha($value);

            case 'alphanumeric':
                return ValidationRules::alphanumeric($value);

            case 'match':
                $matchField = $params[0];
                $matchValue = $this->data[$matchField] ?? null;
                return ValidationRules::match($value, $matchValue);

            case 'in':
                return ValidationRules::in($value, $params);

            case 'notIn':
                return ValidationRules::notIn($value, $params);

            case 'unique':
                $table = $params[0];
                $column = $params[1] ?? $field;
                $exceptId = isset($params[2]) ? (int)$params[2] : null;
                return ValidationRules::unique($value, $table, $column, $exceptId);

            case 'exists':
                $table = $params[0];
                $column = $params[1] ?? 'id';
                return ValidationRules::exists($value, $table, $column);

            case 'date':
                $format = $params[0] ?? 'Y-m-d';
                return ValidationRules::date($value, $format);

            case 'after':
                return ValidationRules::after($value, $params[0]);

            case 'before':
                return ValidationRules::before($value, $params[0]);

            case 'image':
                // Pour les fichiers, on passe le tableau complet
                $fileData = $_FILES[$field] ?? null;
                return $fileData ? ValidationRules::image($fileData) : false;

            case 'fileSize':
                $fileData = $_FILES[$field] ?? null;
                $maxSize = isset($params[0]) ? (int)$params[0] : MAX_FILE_SIZE;
                return $fileData ? ValidationRules::fileSize($fileData, $maxSize) : false;

            case 'strongPassword':
                return ValidationRules::strongPassword($value);

            case 'phoneNumber':
                return ValidationRules::phoneNumber($value);

            case 'validName':
                return ValidationRules::validName($value);

            case 'regex':
                return ValidationRules::regex($value, $params[0]);

            default:
                // Règle inconnue, on considère valide
                return true;
        }
    }

    /**
     * Ajouter une erreur
     * 
     * @param string $field
     * @param string $rule
     * @param array $params
     * @return void
     */
    private function addError(string $field, string $rule, array $params): void
    {
        // Chercher un message personnalisé
        $messageKey = "{$field}.{$rule}";
        
        if (isset($this->customMessages[$messageKey])) {
            $message = $this->customMessages[$messageKey];
        } else {
            $message = $this->defaultMessages[$rule] ?? 'Le champ :field est invalide.';
        }

        // Remplacer les placeholders
        $fieldLabel = $this->fieldLabels[$field] ?? $field;
        $message = str_replace(':field', $fieldLabel, $message);

        // Remplacer les paramètres
        if (!empty($params)) {
            if ($rule === 'fileSize') {
                // Convertir en Mo pour l'affichage
                $params[0] = round($params[0] / (1024 * 1024), 1);
            }
            $message = str_replace(':param', implode(' et ', $params), $message);
        }

        $this->errors[$field][] = $message;
    }

    /**
     * Vérifier si la validation a réussi
     * 
     * @return bool
     */
    public function passes(): bool
    {
        return $this->validate();
    }

    /**
     * Vérifier si la validation a échoué
     * 
     * @return bool
     */
    public function fails(): bool
    {
        return !$this->validate();
    }

    /**
     * Obtenir toutes les erreurs
     * 
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Obtenir les erreurs d'un champ spécifique
     * 
     * @param string $field
     * @return array
     */
    public function getErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Obtenir la première erreur d'un champ
     * 
     * @param string $field
     * @return string|null
     */
    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Vérifier si un champ a des erreurs
     * 
     * @param string $field
     * @return bool
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Obtenir toutes les erreurs sous forme de chaîne
     * 
     * @param string $separator
     * @return string
     */
    public function getErrorsAsString(string $separator = '<br>'): string
    {
        $allErrors = [];
        
        foreach ($this->errors as $fieldErrors) {
            $allErrors = array_merge($allErrors, $fieldErrors);
        }
        
        return implode($separator, $allErrors);
    }
}