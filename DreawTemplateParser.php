<?php

	class DreawTemplateParser {
		private $_DB, $_SESS;

		protected $_template;
		protected $_loop_cache;
		protected $_tags_1;
		protected $_tags_2;
		protected $_tags_3;
		protected $_url = 'http://www.kat.dreaw.cz/template';
		protected $_suffix = '.phtml';
		protected $_only_HTML_parse;

		public function __construct($template_file, $alternative_url = null) {
			//$this->_DB = new DreawDB();
			//$this->_SESS = new DreawSession();

			/*if (file_exists($template_file . $this->_suffix)) {*/
                $this->_template = file_get_contents($this->_url . '/' .  $template_file . $this->_suffix);
            /*}
            else {
                throw new DreawHandler('Master template not found!');
            }*/
		}

		public function addTags_1($tags = array()) {
			$this->_tags_1 = $tags;

			return $this;
		}

		public function addTags_2($tags = array()) {
			$this->_tags_2 = $tags;

			return $this;
		}

		public function addTags_3($tags = array()) {
			$this->_tags_3 = $tags;

			return $this;
		}

		public function parse() {
			if ($this->_isData()) {
				if ($this->_isData(3)) {
					foreach ($this->_tags_3 as $key => $data_value) {
						if (strpos($this->_template, $key)) { // if is tag in template
							$repeat = $this->_command($this->_template, '{' . $key . '|foreach}', '{' . $key . '|/foreach}');
							$foreach_in_template = '{' . $key . '|foreach}' . $repeat . '{' . $key . '|/foreach}';

							foreach ($data_value as $row) {
								$loop_row = $repeat;

								foreach ($row as $data => $value) {
									$loop_row = $this->_commandEvaluation($data, $value, $loop_row);
								}

								$this->_loop_cache .= $loop_row;
							}

							$this->_template = str_replace($foreach_in_template, $this->_loop_cache, $this->_template);
						}
					}
				}
			}

			return $this;
		}

		public function view() {
			print $this->_template;

			return $this;
		}

		protected function _isData($num = 4) {
			if ($num == 1) {
				if (!empty($this->_tags_1)) {
					return true;
				}

				return false;
			}

			else if ($num == 2) {
				if (!empty($this->_tags_2)) {
					return true;
				}

				return false;
			}

			else if ($num == 3) {
				if (!empty($this->_tags_3)) {
					return true;
				}

				return false;
			}

			else if ($num == 4) {
				if (!empty($this->_tags_1) || !empty($this->_tags_2) || !empty($this->_tags_3)) {
					return true;
				}

				return false;
			}

			else {
				throw new ErrorHandler('ATTACK!');
			}
		}

		protected function _commandEvaluation($tag, $tag_data, $source) {
			if (strpos($source, '{' . $tag . '}')) {
				$source = str_replace('{' . $tag . '}', $tag_data, $source);
			}

			return $source;
		}

		protected function _command($string, $start, $end) {
            $string = ' ' . $string;
            $ini = strpos($string, $start);

            if ($ini == 0) return '';

            $ini += strlen($start);
            $len = strpos($string, $end, $ini) - $ini;

            return substr($string, $ini, $len);
        }

		public function debugStart ($what) {
            if ($what == 'full') {
                $this->_fullTime = microtime(true);
            }
            else {
                $this->parseTime = microtime(true);
            }

            return $this;
        }

        public function debugEnd ($what) {
            if ($what == 'full') {
                echo '<p><strong>Doba potřebná ke kompletnímu zobrazení stránky: ' . number_format(microtime(false) - $this->fullTime, 5) . ' s</strong></p>';
            }
            else {
                echo '<p><strong>Doba potřebná k vykreslení parserem: ' . number_format(microtime(true) - $this->parseTime, 5) . ' s</strong></p>';
            }

            return $this;
        }
	}