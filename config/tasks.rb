namespace :nginx do
	desc "Restarts nginx"
	task :restart do
		run "sudo /etc/init.d/nginx reload"
	end
end

namespace :phpfpm do
	desc" Restarts PHP-FPM"
	task :restart do
		run "sudo /etc/init.d/php-fpm restart"
	end
end

namespace :apc do
	desc <<-DESC
		Create a temporary PHP file to clear APC cache, call it (using curl) and removes it
		This task must be triggered AFTER the deployment to clear APC cache
	DESC
	task :clear_cache, :roles => :app do
		apc_file = "#{current_release}#{apc_webroot}/apc_clear.php"
		curl_options = "-s"
		put "<?php apc_clear_cache(); apc_clear_cache('user'); apc_clear_cache('opcode'); ?>", apc_file, :mode => 0644
		run "curl #{curl_options} #{url_base}/apc_clear.php && rm -f #{apc_file}"
	end
end

namespace :db do
	desc "Imports DB data"
	task :update_database, :roles => :db, :only => { :primary => true } do
    	filename_wp = "wp.#{Time.now.strftime '%Y%m%dT%H%M%S'}.sql"
    	filename_data = "data.#{Time.now.strftime '%Y%m%dT%H%M%S'}.sql"
    	remote_path_wp = "/tmp/#{filename_wp}"
    	remote_path_data = "/tmp/#{filename_data}"
    	on_rollback {
	        delete remote_path_wp
	        delete remote_path_data
	    }
	    system "mysqldump -urps_user -pAwer871 -n --result-file='#{remote_path_wp}' avirtu2_raritwp"
	    system "mysqldump -urps_user -pAwer871 -n --result-file='#{remote_path_data}' avirtu2_raritdata"

		upload("#{remote_path_wp}", "#{remote_path_wp}", {:hosts => "avirtualhome.com", :via => :scp})
		upload("#{remote_path_wp}", "#{remote_path_data}", {:hosts => "avirtualhome.com", :via => :scp})

	    run "mysql --user=rps_user --password=Awer871 rps_wordpress < #{remote_path_wp}"
	    run "mysql --user=rps_user --password=Awer871 rps_data < #{remote_path_data}"
	end
end